<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Support;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Mine\AppStore\Plugin;

final class PluginPathGuard
{
    public function __construct(
        private readonly PluginIdentifier $identifier
    ) {}

    public function pluginRoot(): string
    {
        $root = realpath(Plugin::PLUGIN_PATH);
        if ($root === false || ! is_dir($root)) {
            throw new BusinessException(ResultCode::FAIL, '插件根目录不存在');
        }

        return $root;
    }

    public function pluginPath(string $identifier, bool $mustExist = false): string
    {
        [$vendor, $name] = $this->identifier->vendorAndName($identifier);
        $root = $this->pluginRoot();
        $path = $root . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $name;

        if ($mustExist) {
            $realPath = realpath($path);
            if ($realPath === false || ! is_dir($realPath)) {
                throw new BusinessException(ResultCode::NOT_FOUND, '插件目录不存在');
            }
            if (! $this->isPathInside($root, $realPath) || $this->containsSymlink($root, $realPath)) {
                throw new BusinessException(ResultCode::FORBIDDEN, '插件目录不安全');
            }
            return $realPath;
        }

        if (! $this->isPathInside($root, $path)) {
            throw new BusinessException(ResultCode::FORBIDDEN, '插件目录不安全');
        }

        return $path;
    }

    public function frontDirectory(): string
    {
        $configured = (string) Plugin::getConfig('front_directory', BASE_PATH . '/web');
        if (! $this->isAbsolutePath($configured)) {
            $configured = BASE_PATH . DIRECTORY_SEPARATOR . $configured;
        }

        $realPath = realpath($configured);
        if ($realPath === false || ! is_dir($realPath)) {
            throw new BusinessException(ResultCode::FAIL, '前端目录不存在');
        }

        return $realPath;
    }

    public function ensureSafeWorkDir(string $path, string $base): string
    {
        $realPath = realpath($path);
        $realBase = realpath($base);
        if ($realPath === false || $realBase === false || ! is_dir($realPath) || ! $this->isPathInside($realBase, $realPath)) {
            throw new BusinessException(ResultCode::FORBIDDEN, '工作目录不安全');
        }

        return $realPath;
    }

    public function assertSafeZipEntries(\ZipArchive $zip, string $destination): void
    {
        for ($index = 0; $index < $zip->numFiles; ++$index) {
            $name = $zip->getNameIndex($index);
            if (
                ! is_string($name)
                || $this->isUnsafeZipEntryName($name)
                || $this->isZipEntrySymlink($zip, $index)
                || ! $this->isZipEntryDestinationSafe($destination, $name)
            ) {
                throw new BusinessException(ResultCode::FORBIDDEN, 'ZIP 文件包含不安全路径');
            }
        }
    }

    public function isPathInside(string $basePath, string $targetPath): bool
    {
        $base = $this->normalizeFilesystemPath($basePath);
        $target = $this->normalizeFilesystemPath($targetPath);

        if (DIRECTORY_SEPARATOR === '\\') {
            $base = strtolower($base);
            $target = strtolower($target);
        }

        return $target === $base || str_starts_with($target, $base . '/');
    }

    private function containsSymlink(string $base, string $target): bool
    {
        $base = $this->normalizeFilesystemPath($base);
        $target = $this->normalizeFilesystemPath($target);
        if (! str_starts_with($target, $base)) {
            return true;
        }

        $relative = trim(substr($target, strlen($base)), '/');
        $current = $base;
        if ($relative === '') {
            return is_link($current);
        }

        foreach (explode('/', $relative) as $segment) {
            $current .= '/' . $segment;
            if (is_link($current)) {
                return true;
            }
        }

        return false;
    }

    private function isUnsafeZipEntryName(string $name): bool
    {
        if ($name === '' || str_contains($name, "\0") || str_contains($name, '\\')) {
            return true;
        }

        $normalized = str_replace('\\', '/', $name);
        if ($normalized[0] === '/' || preg_match('/\A[A-Za-z]:/', $normalized)) {
            return true;
        }

        $normalized = trim($normalized, '/');
        if ($normalized === '') {
            return true;
        }

        foreach (explode('/', $normalized) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                return true;
            }
        }

        return false;
    }

    private function isZipEntryDestinationSafe(string $destination, string $name): bool
    {
        $targetPath = $destination . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, trim($name, '/'));
        if (! $this->isPathInside($destination, $targetPath)) {
            return false;
        }

        $currentPath = $destination;
        foreach (explode('/', trim($name, '/')) as $segment) {
            $currentPath .= DIRECTORY_SEPARATOR . $segment;
            if (is_link($currentPath)) {
                return false;
            }
            if (file_exists($currentPath)) {
                $realPath = realpath($currentPath);
                if ($realPath === false || ! $this->isPathInside($destination, $realPath)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isZipEntrySymlink(\ZipArchive $zip, int $index): bool
    {
        $opsys = 0;
        $attributes = 0;
        if (! $zip->getExternalAttributesIndex($index, $opsys, $attributes)) {
            return false;
        }

        return (($attributes >> 16) & 0xF000) === 0xA000;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || preg_match('/\A[A-Za-z]:[\/\\\\]/', $path) === 1
            || str_starts_with($path, '\\\\');
    }

    private function normalizeFilesystemPath(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);
        $normalized = preg_replace('#/+#', '/', $normalized) ?? $normalized;
        return rtrim($normalized, '/');
    }
}
