<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Command;

use Hyperf\Command\Annotation\Command as CommandAnnotation;
use Hyperf\Command\Command;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Redis\RedisFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Throwable;

#[CommandAnnotation]
class InstallCommand extends Command
{
    private const FRONTEND_REPOSITORY = 'https://github.com/mineadmin/mineadmin-vue';

    protected ?string $name = 'mine:install';

    protected string $description = 'Install MineAdmin data and configured extensions';

    public function handle(): int
    {
        $this->output->title('MineAdmin Install');

        if (! $this->checkEnvironment()) {
            return self::FAILURE;
        }

        $this->installFrontend();

        if (! $this->runRequiredCommand('migrate')) {
            return self::FAILURE;
        }

        if (! $this->runRequiredCommand('db:seed')) {
            return self::FAILURE;
        }

        if (! $this->installExtensions()) {
            return self::FAILURE;
        }

        $this->output->success('MineAdmin Install Success');

        return self::SUCCESS;
    }

    private function checkEnvironment(): bool
    {
        $this->output->section('Environment Check');

        if (! file_exists(BASE_PATH . '/.env')) {
            $this->output->error('The .env file does not exist. Please create .env before installing MineAdmin.');
            return false;
        }
        $this->output->writeln('<info>[OK]</info> .env file exists');

        try {
            Db::select('SELECT 1');
            $this->output->writeln('<info>[OK]</info> Database connection is available');
        } catch (Throwable $throwable) {
            $this->output->error('Database connection failed: ' . $throwable->getMessage());
            return false;
        }

        try {
            $this->redisFactory()->get('default')->ping();
            $this->output->writeln('<info>[OK]</info> Redis connection is available');
        } catch (Throwable $throwable) {
            $this->output->error('Redis connection failed: ' . $throwable->getMessage());
            return false;
        }

        return true;
    }

    private function installFrontend(): void
    {
        $this->output->section('Frontend Install');

        try {
            if (! $this->confirm('Download the frontend project to the web directory?', false)) {
                $this->output->writeln('<comment>[SKIP]</comment> Skip frontend download');
                return;
            }

            if (! $this->downloadFrontend()) {
                return;
            }

            if (! $this->confirm('Install frontend dependencies?', false)) {
                $this->output->writeln('<comment>[SKIP]</comment> Skip frontend dependencies install');
                return;
            }

            $this->installFrontendDependencies();
        } catch (Throwable $throwable) {
            $this->output->warning('Frontend install skipped: ' . $throwable->getMessage());
        }
    }

    private function downloadFrontend(): bool
    {
        $frontendPath = $this->frontendPath();

        if (! $this->isGitAvailable()) {
            $this->output->warning(sprintf(
                'Git was not detected. Please manually download %s to %s.',
                self::FRONTEND_REPOSITORY,
                $frontendPath
            ));
            return false;
        }

        if (file_exists($frontendPath) && ! is_dir($frontendPath)) {
            $this->output->warning(sprintf('Path %s already exists and is not a directory. Skip frontend download.', $frontendPath));
            return false;
        }

        if (is_dir($frontendPath) && ! $this->isDirectoryEmpty($frontendPath)) {
            $this->output->warning(sprintf(
                'Directory %s already exists and is not empty. Please handle it manually and rerun the command.',
                $frontendPath
            ));
            return false;
        }

        $this->output->writeln(sprintf('<comment>[DOWNLOAD]</comment> %s', self::FRONTEND_REPOSITORY));
        $result = $this->runProcess([
            'git',
            'clone',
            '--depth=1',
            self::FRONTEND_REPOSITORY,
            $frontendPath,
        ], BASE_PATH, 600);

        if (! $result['successful']) {
            $this->output->error('Frontend download failed: ' . $result['error']);
            $this->output->writeln('<comment>[SKIP]</comment> Skip frontend install and continue backend install');
            return false;
        }

        $this->removeFrontendGitDirectory();
        $this->output->writeln(sprintf('<info>[OK]</info> Frontend downloaded to %s', $frontendPath));

        return true;
    }

    private function removeFrontendGitDirectory(): void
    {
        $gitPath = $this->frontendPath() . DIRECTORY_SEPARATOR . '.git';
        if (! is_dir($gitPath)) {
            return;
        }

        try {
            (new Filesystem())->remove($gitPath);
            $this->output->writeln('<info>[OK]</info> Removed frontend .git directory');
        } catch (Throwable $throwable) {
            $this->output->warning('Failed to remove frontend .git directory: ' . $throwable->getMessage());
        }
    }

    private function installFrontendDependencies(): void
    {
        $missing = [];
        if (! $this->isNodeAvailable()) {
            $missing[] = 'Node.js';
        }
        if (! $this->isPnpmAvailable()) {
            $missing[] = 'pnpm';
        }

        if ($missing !== []) {
            $this->output->warning(sprintf(
                'Missing %s. Please manually install frontend dependencies in %s.',
                implode(' and ', $missing),
                $this->frontendPath()
            ));
            return;
        }

        $this->output->writeln('<comment>[INSTALL]</comment> pnpm install');
        $result = $this->runProcess(['pnpm', 'install'], $this->frontendPath(), 1200);
        if (! $result['successful']) {
            $this->output->error('Frontend dependencies install failed: ' . $result['error']);
            $this->output->writeln('<comment>[SKIP]</comment> Continue backend install');
            return;
        }

        $this->output->writeln('<info>[OK]</info> Frontend dependencies installed');
    }

    private function isGitAvailable(): bool
    {
        return $this->isCommandAvailable('git');
    }

    private function isNodeAvailable(): bool
    {
        return $this->isCommandAvailable('node');
    }

    private function isPnpmAvailable(): bool
    {
        return $this->isCommandAvailable('pnpm');
    }

    private function isCommandAvailable(string $command): bool
    {
        return $this->runProcess([$command, '--version'], BASE_PATH, 15, false)['successful'];
    }

    /**
     * @return array{successful: bool, error: string}
     */
    private function runProcess(array $command, ?string $cwd = null, int $timeout = 300, bool $displayOutput = true): array
    {
        try {
            $process = new Process($command, $cwd);
            $process->setTimeout($timeout);
            $process->run(function (string $type, string $buffer) use ($displayOutput): void {
                if ($displayOutput) {
                    $this->output->write($buffer);
                }
            });
        } catch (Throwable $throwable) {
            return [
                'successful' => false,
                'error' => $throwable->getMessage(),
            ];
        }

        if ($process->isSuccessful()) {
            return [
                'successful' => true,
                'error' => '',
            ];
        }

        return [
            'successful' => false,
            'error' => trim($process->getErrorOutput())
                ?: trim($process->getOutput())
                    ?: sprintf('Exit code %d', $process->getExitCode()),
        ];
    }

    private function frontendPath(): string
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'web';
    }

    private function isDirectoryEmpty(string $path): bool
    {
        $items = scandir($path);
        if ($items === false) {
            return false;
        }

        return array_diff($items, ['.', '..']) === [];
    }

    private function runRequiredCommand(string $command): bool
    {
        $this->output->section(sprintf('Run %s', $command));

        try {
            $exitCode = $this->call($command);
        } catch (Throwable $throwable) {
            $this->output->error(sprintf('Command %s failed: %s', $command, $throwable->getMessage()));
            return false;
        }

        if ($exitCode !== self::SUCCESS) {
            $this->output->error(sprintf('Command %s failed with exit code %d.', $command, $exitCode));
            return false;
        }

        $this->output->writeln(sprintf('<info>[OK]</info> Command %s completed', $command));

        return true;
    }

    private function installExtensions(): bool
    {
        $this->output->section('Install Extensions');

        $extensions = $this->config()->get('mine-extension.auto_install_list', []);
        if (! is_array($extensions)) {
            $this->output->error('Config mine-extension.auto_install_list must be an array.');
            return false;
        }

        if ($extensions === []) {
            $this->output->writeln('<comment>[SKIP]</comment> auto_install_list is empty');
            return true;
        }

        $failed = [];
        foreach ($extensions as $key => $extension) {
            $path = $this->normalizeExtensionPath($extension, $key);
            if ($path === null) {
                $this->output->warning(sprintf('Skip invalid extension config item: %s', (string) $key));
                continue;
            }

            $this->output->writeln(sprintf('<comment>[INSTALL]</comment> %s', $path));
            if (! $this->installExtension($path)) {
                $failed[] = $path;
            }
        }

        if ($failed !== []) {
            $this->output->warning(sprintf(
                'The following extensions failed and were skipped: %s',
                implode(', ', $failed)
            ));
        }

        return true;
    }

    private function installExtension(string $path): bool
    {
        try {
            $exitCode = $this->call('mine-extension:install', [
                'path' => $path,
                '--yes' => true,
            ]);
        } catch (Throwable $throwable) {
            $this->output->error(sprintf(
                'Plugin %s install failed: %s. Skip it and continue.',
                $path,
                $throwable->getMessage()
            ));
            return false;
        }

        if ($exitCode !== self::SUCCESS) {
            $this->output->error(sprintf(
                'Plugin %s install failed with exit code %d. Skip it and continue.',
                $path,
                $exitCode
            ));
            return false;
        }

        $this->output->writeln(sprintf('<info>[OK]</info> Plugin %s installed', $path));

        return true;
    }

    private function normalizeExtensionPath(mixed $extension, int|string $key): ?string
    {
        if (is_string($extension) || is_numeric($extension)) {
            $path = trim((string) $extension);
            return $path === '' ? null : $path;
        }

        if (is_array($extension) && isset($extension['path']) && is_string($extension['path'])) {
            $path = trim($extension['path']);
            return $path === '' ? null : $path;
        }

        if (is_string($key) && ($extension === true || $extension === null)) {
            $path = trim($key);
            return $path === '' ? null : $path;
        }

        return null;
    }

    private function config(): ConfigInterface
    {
        return ApplicationContext::getContainer()->get(ConfigInterface::class);
    }

    private function redisFactory(): RedisFactory
    {
        return ApplicationContext::getContainer()->get(RedisFactory::class);
    }
}