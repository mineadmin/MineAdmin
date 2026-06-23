<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Composer\InstalledVersions;
use Mine\AppStore\Plugin;
use Plugin\MineAdmin\AppStore\Enum\TerminalAction;
use Plugin\MineAdmin\AppStore\Enum\TerminalStatus;
use Plugin\MineAdmin\AppStore\Support\PluginPathGuard;
use Plugin\MineAdmin\AppStore\Support\SafeProcessRunner;

class TerminalCommandService
{
    private const COMPOSER_PACKAGE_PATTERN = '/\A[a-z0-9_.-]+\/[a-z0-9_.-]+\z/';

    private const NPM_PACKAGE_PATTERN = '/\A(@[a-z0-9][a-z0-9._-]*\/)?[a-z0-9][a-z0-9._-]*\z/i';

    private const VERSION_CONSTRAINT_PATTERN = '/\A[0-9A-Za-z.*+~^<>=|!,_-]+\z/';

    public function __construct(
        private readonly TerminalRuntimeStore $store,
        private readonly SafeProcessRunner $runner,
        private readonly PluginPathGuard $pathGuard,
        private readonly Service $appStoreService
    ) {}

    public function execute(string $taskNo): void
    {
        $task = $this->store->getInternalTask($taskNo);
        if ($task === []) {
            return;
        }

        $action = TerminalAction::tryFrom((string) ($task['action'] ?? ''));
        if (! $action instanceof TerminalAction) {
            $this->store->markFailed($taskNo, '终端动作不支持');
            return;
        }

        try {
            $this->store->acquireActionLock($taskNo, $this->lockName($action, $task['identifier'] ?? null));
            $this->store->markRunning($taskNo, 'prepare', 5);
            $this->append($taskNo, 'system', sprintf('开始执行：%s', $action->label()));

            match ($action) {
                TerminalAction::Download => $this->download($taskNo, $task),
                TerminalAction::Install => $this->install($taskNo, $task),
                TerminalAction::Uninstall => $this->uninstall($taskNo, $task),
                TerminalAction::FrontendDeps => $this->frontendDeps($taskNo, $task),
                TerminalAction::BackendDeps => $this->backendDeps($taskNo, $task),
                TerminalAction::InstallPnpm => $this->installPnpm($taskNo),
                TerminalAction::CheckEnvironment => $this->checkEnvironment($taskNo),
            };

            if ($this->store->shouldCancel($taskNo)) {
                $this->store->markCancelled($taskNo);
                return;
            }

            $this->store->markSuccess($taskNo);
        } catch (BusinessException $e) {
            if ($this->shouldKeepCancelled($taskNo)) {
                return;
            }
            $this->store->markFailed($taskNo, $e->getResponse()->message ?? $e->getMessage());
        } catch (\Throwable $e) {
            if ($this->shouldKeepCancelled($taskNo)) {
                return;
            }
            $this->store->markFailed($taskNo, $e->getMessage());
        }
    }

    private function download(string $taskNo, array $task): void
    {
        $identifier = (string) $task['identifier'];
        $version = (string) $task['version'];
        $this->step($taskNo, 'download', 20, sprintf('开始下载插件 %s:%s', $identifier, $version));
        $this->appStoreService->download([
            'identifier' => $identifier,
            'version' => $version,
        ]);
        Plugin::forceRefreshJsonPath();
        $this->step($taskNo, 'downloaded', 80, '插件下载完成');
    }

    private function install(string $taskNo, array $task): void
    {
        $identifier = (string) $task['identifier'];
        $version = (string) $task['version'];
        $expectedPath = $this->pathGuard->pluginPath($identifier);
        if (! is_dir($expectedPath)) {
            $this->step($taskNo, 'download', 15, sprintf('插件未下载，开始下载 %s:%s', $identifier, $version));
            $this->appStoreService->download([
                'identifier' => $identifier,
                'version' => $version,
            ]);
            Plugin::forceRefreshJsonPath();
            $this->step($taskNo, 'downloaded', 30, '插件下载完成');
        }

        $pluginPath = $this->pathGuard->pluginPath($identifier, true);

        $this->step($taskNo, 'validate', 35, sprintf('校验插件目录：%s', $pluginPath));
        $this->validateManagedManifest($this->readMineJson($identifier));

        $this->step($taskNo, 'install', 35, '调用插件安装流程');
        $this->appStoreService->install([
            'identifier' => $identifier,
            'version' => $version,
        ]);
        $this->step($taskNo, 'installed', 90, '插件安装流程已完成');
    }

    private function uninstall(string $taskNo, array $task): void
    {
        $identifier = (string) $task['identifier'];
        $version = (string) $task['version'];
        if ($identifier === 'mine-admin/app-store') {
            throw new BusinessException(ResultCode::FORBIDDEN, '应用市场插件不允许通过终端卸载');
        }

        $this->pathGuard->pluginPath($identifier, true);
        $this->validateManagedManifest($this->readMineJson($identifier));
        $this->step($taskNo, 'uninstall', 35, sprintf('开始卸载插件 %s', $identifier));
        $this->appStoreService->unInstall([
            'identifier' => $identifier,
            'version' => $version,
        ]);
        $this->step($taskNo, 'uninstalled', 90, '插件卸载流程已完成');
    }

    private function frontendDeps(string $taskNo, array $task): void
    {
        $identifier = (string) $task['identifier'];
        $info = $this->readMineJson($identifier);
        $dependencies = $info['package']['dependencies'] ?? [];
        if (! is_array($dependencies) || $dependencies === []) {
            $this->step($taskNo, 'frontend_deps', 100, '插件没有声明前端依赖');
            return;
        }

        $frontDirectory = $this->pathGuard->frontDirectory();
        $this->pathGuard->ensureSafeWorkDir($frontDirectory, BASE_PATH);
        $frontBinConfig = Plugin::getConfig('front-tool', []);
        $type = $frontBinConfig['type'] ?? 'pnpm';
        $bin = $frontBinConfig['bin'] ?? $type;
        $installCommand = match ($type) {
            'npm', 'pnpm' => 'install',
            'yarn' => 'add',
            default => throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '不支持的前端包管理工具'),
        };

        $command = [$this->runner->findBinary((string) $bin), $installCommand];
        foreach ($dependencies as $package => $version) {
            $command[] = $this->frontendPackageArgument((string) $package, (string) $version);
        }

        $this->step($taskNo, 'frontend_deps', 25, sprintf('开始安装前端依赖，共 %d 个', count($dependencies)));
        $exitCode = $this->runProcess($taskNo, $command, $frontDirectory, 1200);
        if ($exitCode !== 0) {
            throw new \RuntimeException(sprintf('前端依赖安装失败，退出码：%d', $exitCode));
        }
        $this->step($taskNo, 'frontend_deps_done', 95, '前端依赖安装完成');
    }

    private function backendDeps(string $taskNo, array $task): void
    {
        $identifier = (string) $task['identifier'];
        $info = $this->readMineJson($identifier);
        $requires = $info['composer']['require'] ?? [];
        if (! is_array($requires) || $requires === []) {
            $this->step($taskNo, 'backend_deps', 100, '插件没有声明后端依赖');
            return;
        }

        $packages = [];
        foreach ($requires as $package => $version) {
            if (InstalledVersions::isInstalled((string) $package)) {
                $this->append($taskNo, 'system', sprintf('后端依赖已存在，跳过：%s', $package));
                continue;
            }
            $packages[] = $this->composerPackageArgument((string) $package, (string) $version);
        }

        if ($packages === []) {
            $this->step($taskNo, 'backend_deps', 100, '后端依赖均已安装');
            return;
        }

        $composerBin = $this->runner->findBinary((string) Plugin::getConfig('composer.bin', 'composer'));
        $command = array_merge([$composerBin, 'require'], $packages);

        $this->step($taskNo, 'backend_deps', 25, sprintf('开始安装后端依赖，共 %d 个', count($packages)));
        $exitCode = $this->runProcess($taskNo, $command, BASE_PATH, 1800);
        if ($exitCode !== 0) {
            throw new \RuntimeException(sprintf('后端依赖安装失败，退出码：%d', $exitCode));
        }
        $this->step($taskNo, 'backend_deps_done', 95, '后端依赖安装完成');
    }

    private function installPnpm(string $taskNo): void
    {
        $this->step($taskNo, 'install_pnpm', 20, '开始检测 pnpm');
        $npmBin = DIRECTORY_SEPARATOR === '\\' ? 'npm.cmd' : 'npm';
        $exitCode = $this->runProcess($taskNo, [$npmBin, 'install', '-g', 'pnpm'], BASE_PATH, 1200);
        if ($exitCode !== 0) {
            throw new \RuntimeException(sprintf('pnpm 安装失败，退出码：%d', $exitCode));
        }
        $this->step($taskNo, 'install_pnpm_done', 95, 'pnpm 安装完成');
    }

    private function checkEnvironment(string $taskNo): void
    {
        $checks = [
            ['PHP 版本', PHP_VERSION],
            ['应用根目录', BASE_PATH],
            ['插件根目录', $this->pathGuard->pluginRoot()],
            ['前端目录', $this->pathGuard->frontDirectory()],
            ['Composer 命令', (string) Plugin::getConfig('composer.bin', 'composer')],
            ['前端包管理工具', json_encode(Plugin::getConfig('front-tool', []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}'],
        ];

        foreach ($checks as [$name, $value]) {
            $this->append($taskNo, 'system', sprintf('%s：%s', $name, $value));
        }
        $this->step($taskNo, 'checked', 95, '环境检查完成');
    }

    private function readMineJson(string $identifier): array
    {
        $pluginPath = $this->pathGuard->pluginPath($identifier, true);
        $jsonPath = $pluginPath . DIRECTORY_SEPARATOR . 'mine.json';
        if (! is_file($jsonPath)) {
            throw new BusinessException(ResultCode::NOT_FOUND, 'mine.json 不存在');
        }

        $content = file_get_contents($jsonPath);
        $info = is_string($content) ? json_decode($content, true) : null;
        if (! is_array($info)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, 'mine.json 格式不正确');
        }
        if (($info['name'] ?? null) !== $identifier) {
            throw new BusinessException(ResultCode::FORBIDDEN, 'mine.json 插件标识与目录不一致');
        }

        return $info;
    }

    private function runProcess(string $taskNo, array $command, string $workingDirectory, int $timeout): int
    {
        return $this->runner->run(
            $command,
            $workingDirectory,
            function (string $stream, string $message, ?int $pid = null) use ($taskNo): void {
                if ($pid !== null) {
                    $this->store->setProcessPid($taskNo, $pid);
                }
                $level = $stream === 'stderr' ? 'error' : 'info';
                $this->store->appendLog($taskNo, $level, $stream, $message);
            },
            fn (): bool => $this->store->shouldCancel($taskNo),
            $timeout,
            [],
            function () use ($taskNo): void {
                $this->store->touchTask($taskNo);
            }
        );
    }

    private function composerPackageArgument(string $package, string $version): string
    {
        $package = strtolower(trim($package));
        $version = trim($version);
        if (! preg_match(self::COMPOSER_PACKAGE_PATTERN, $package) || ! preg_match(self::VERSION_CONSTRAINT_PATTERN, $version)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '后端依赖声明包含不安全内容');
        }

        return $package . ':' . $version;
    }

    private function frontendPackageArgument(string $package, string $version): string
    {
        $package = trim($package);
        $version = trim($version);
        if (! preg_match(self::NPM_PACKAGE_PATTERN, $package) || ! preg_match(self::VERSION_CONSTRAINT_PATTERN, $version)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '前端依赖声明包含不安全内容');
        }

        return $package . '@' . $version;
    }

    private function step(string $taskNo, string $step, int $progress, string $message): void
    {
        if ($this->store->shouldCancel($taskNo)) {
            $this->store->markCancelled($taskNo);
            throw new \RuntimeException('任务已取消');
        }

        $this->store->updateTask($taskNo, [
            'current_step' => $step,
            'progress' => $progress,
        ]);
        $this->append($taskNo, 'system', $message);
    }

    private function append(string $taskNo, string $stream, string $message): void
    {
        $this->store->appendLog($taskNo, 'info', $stream, $message);
    }

    private function lockName(TerminalAction $action, ?string $identifier): string
    {
        if ($action === TerminalAction::CheckEnvironment) {
            return 'environment';
        }

        return 'global-mutating';
    }

    private function validateManagedManifest(array $info): void
    {
        if (! empty($info['composer']['script'])) {
            throw new BusinessException(ResultCode::FORBIDDEN, '终端安装不支持执行 composer.script shell 脚本');
        }

        $requires = $info['composer']['require'] ?? [];
        if (is_array($requires)) {
            foreach ($requires as $package => $version) {
                $this->composerPackageArgument((string) $package, (string) $version);
            }
        }

        $dependencies = $info['package']['dependencies'] ?? [];
        if (is_array($dependencies)) {
            foreach ($dependencies as $package => $version) {
                $this->frontendPackageArgument((string) $package, (string) $version);
            }
        }
    }

    private function shouldKeepCancelled(string $taskNo): bool
    {
        $task = $this->store->getInternalTask($taskNo);
        if (($task['status'] ?? null) === TerminalStatus::Cancelled->value) {
            return true;
        }
        if ($this->store->shouldCancel($taskNo)) {
            $this->store->markCancelled($taskNo);
            return true;
        }

        return false;
    }
}
