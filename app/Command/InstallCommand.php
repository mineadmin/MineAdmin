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
use Throwable;

#[CommandAnnotation]
class InstallCommand extends Command
{
    protected ?string $name = 'mine:install';

    protected string $description = 'Install MineAdmin data and configured extensions';

    public function handle(): int
    {
        $this->output->title('MineAdmin Install');

        if (! $this->checkEnvironment()) {
            return self::FAILURE;
        }

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