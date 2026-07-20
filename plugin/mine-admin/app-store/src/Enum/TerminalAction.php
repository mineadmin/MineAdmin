<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Enum;

enum TerminalAction: string
{
    case Download = 'download';
    case Install = 'install';
    case Uninstall = 'uninstall';
    case FrontendDeps = 'frontend_deps';
    case BackendDeps = 'backend_deps';
    case InstallPnpm = 'install_pnpm';
    case CheckEnvironment = 'check_environment';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function permissionCode(): string
    {
        return match ($this) {
            self::Download => 'plugin:store:terminal:download',
            self::Install => 'plugin:store:terminal:install',
            self::Uninstall => 'plugin:store:terminal:uninstall',
            self::FrontendDeps => 'plugin:store:terminal:frontend-deps',
            self::BackendDeps => 'plugin:store:terminal:backend-deps',
            self::InstallPnpm => 'plugin:store:terminal:pnpm',
            self::CheckEnvironment => 'plugin:store:terminal',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Download => '下载插件',
            self::Install => '安装插件',
            self::Uninstall => '卸载插件',
            self::FrontendDeps => '安装前端依赖',
            self::BackendDeps => '安装后端依赖',
            self::InstallPnpm => '安装 pnpm',
            self::CheckEnvironment => '检查环境',
        };
    }

    public function requiresIdentifier(): bool
    {
        return match ($this) {
            self::Download,
            self::Install,
            self::Uninstall,
            self::FrontendDeps,
            self::BackendDeps => true,
            self::InstallPnpm,
            self::CheckEnvironment => false,
        };
    }
}
