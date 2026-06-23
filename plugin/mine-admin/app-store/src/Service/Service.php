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

namespace Plugin\MineAdmin\AppStore\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Mine\AppStore\Exception\PluginNotFoundException;
use Mine\AppStore\Plugin;
use Mine\AppStore\Service\Impl\AppStoreServiceImpl;

class Service
{
    private const IDENTIFIER_PATTERN = '/\A[A-Za-z0-9_-]+\/[A-Za-z0-9_-]+\z/';

    private const ZIP_UNIX_SYMLINK_MODE = 0xA000;

    public function download(array $params): bool
    {
        if (empty($params['identifier']) || empty($params['version'])) {
            $this->throwParamsFail();
        }
        $identifier = $this->normalizeIdentifier($params['identifier']);

        $service = make(AppStoreServiceImpl::class);

        if (! is_dir(Plugin::PLUGIN_PATH . '/' . $identifier)) {
            $result = $service->download($identifier, $params['version']);
            if (! $result) {
                $this->throwDownloadFail();
            }
        }

        return true;
    }

    public function install(array $params): bool
    {
        if (empty($params['identifier']) || empty($params['version'])) {
            $this->throwParamsFail();
        }
        $identifier = $this->normalizeIdentifier($params['identifier']);

        $path = Plugin::PLUGIN_PATH . '/' . $identifier;

        if (file_exists($path . '/install.lock')) {
            $this->throwAppInstalled();
        }

        try {
            Plugin::forceRefreshJsonPath();
            Plugin::install($identifier);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return true;
    }

    public function unInstall(array $params): bool
    {
        if (empty($params['identifier']) || empty($params['version'])) {
            $this->throwParamsFail();
        }
        $identifier = $this->normalizeIdentifier($params['identifier']);

        $path = Plugin::PLUGIN_PATH . '/' . $identifier;

        if (! file_exists($path . '/install.lock')) {
            $this->throwAppNoInstall();
        }

        try {
            Plugin::forceRefreshJsonPath();
            Plugin::uninstall($identifier);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage());
        }
        return true;
    }

    /**
     * @throws PluginNotFoundException
     */
    public function getLocalAppInstallList(): array
    {
        $list = Plugin::getPluginJsonPaths();

        $items = [];
        foreach ($list as $splFileInfo) {
            $info = Plugin::read($splFileInfo->getRelativePath());
            if (! empty($info)) {
                $items[$info['name']] = [
                    'status' => $info['status'],
                    'version' => $info['version'],
                    'description' => $info['description'],
                    'author' => $info['author'],
                ];
            }
        }
        return $items;
    }

    public function uploadLocalApp(UploadedFile $file): bool
    {
        $runtimePath = null;
        $zip = null;
        $zipOpened = false;

        try {
            $runtimePath = BASE_PATH . '/runtime/' . uniqid('mineApp', true) . '.zip';
            $file->moveTo($runtimePath);

            $zip = new \ZipArchive();
            if ($zip->open($runtimePath) !== true) {
                throw new \RuntimeException('Failed to open the zip file');
            }
            $zipOpened = true;

            $mineJson = $zip->getFromName('mine.json');
            if ($mineJson === false) {
                throw new \RuntimeException('mine.json not found');
            }
            $json = json_decode(
                $mineJson,
                true,
                512,
                \JSON_THROW_ON_ERROR
            );
            $identifier = $this->normalizeIdentifier($json['name'] ?? null);
            $destination = $this->preparePluginExtractPath($identifier);
            $this->assertSafeZipEntries($zip, $destination);
            if (! $zip->extractTo($destination)) {
                throw new \RuntimeException('Failed to extract the zip file');
            }

            Plugin::forceRefreshJsonPath();
            Plugin::install($identifier);
        } catch (BusinessException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage(), (int) $e->getCode(), $e);
        } finally {
            if ($zipOpened && $zip instanceof \ZipArchive) {
                @$zip->close();
            }
            if ($runtimePath !== null && is_file($runtimePath)) {
                @unlink($runtimePath);
            }
        }
        return true;
    }

    private function normalizeIdentifier(mixed $identifier): string
    {
        if (! is_string($identifier)) {
            $this->throwParamsFail();
        }

        $identifier = trim($identifier);
        if (! preg_match(self::IDENTIFIER_PATTERN, $identifier)) {
            $this->throwParamsFail();
        }

        return $identifier;
    }

    private function preparePluginExtractPath(string $identifier): string
    {
        $pluginRoot = $this->ensureDirectory(Plugin::PLUGIN_PATH);
        [$vendor, $name] = explode('/', $identifier, 2);

        $vendorPath = $pluginRoot . \DIRECTORY_SEPARATOR . $vendor;
        if (is_link($vendorPath)) {
            throw new \RuntimeException('Invalid plugin path');
        }
        if (! is_dir($vendorPath) && ! mkdir($vendorPath, 0o755, true) && ! is_dir($vendorPath)) {
            throw new \RuntimeException('Failed to create plugin vendor directory');
        }
        $realVendorPath = realpath($vendorPath);
        if ($realVendorPath === false || ! $this->isPathInside($pluginRoot, $realVendorPath)) {
            throw new \RuntimeException('Invalid plugin path');
        }

        $destination = $realVendorPath . \DIRECTORY_SEPARATOR . $name;
        if (is_link($destination)) {
            throw new \RuntimeException('Invalid plugin path');
        }
        if (! $this->isPathInside($pluginRoot, $destination)) {
            throw new \RuntimeException('Invalid plugin path');
        }
        if (! is_dir($destination) && ! mkdir($destination, 0o755, true) && ! is_dir($destination)) {
            throw new \RuntimeException('Failed to create plugin directory');
        }

        $realDestination = realpath($destination);
        if ($realDestination === false || ! $this->isPathInside($pluginRoot, $realDestination)) {
            throw new \RuntimeException('Invalid plugin path');
        }

        return $realDestination;
    }

    private function ensureDirectory(string $path): string
    {
        if (! is_dir($path) && ! mkdir($path, 0o755, true) && ! is_dir($path)) {
            throw new \RuntimeException('Failed to create directory');
        }

        $realPath = realpath($path);
        if ($realPath === false || ! is_dir($realPath)) {
            throw new \RuntimeException('Invalid directory');
        }

        return $realPath;
    }

    private function assertSafeZipEntries(\ZipArchive $zip, string $destination): void
    {
        for ($index = 0; $index < $zip->numFiles; ++$index) {
            $name = $zip->getNameIndex($index);
            if (
                ! is_string($name)
                || $this->isUnsafeZipEntryName($name)
                || $this->isZipEntrySymlink($zip, $index)
                || ! $this->isZipEntryDestinationSafe($destination, $name)
            ) {
                throw new \RuntimeException('Invalid zip entry path');
            }
        }
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
        $targetPath = $this->zipEntryDestination($destination, $name);
        if (! $this->isPathInside($destination, $targetPath)) {
            return false;
        }

        $currentPath = $destination;
        foreach (explode('/', trim($name, '/')) as $segment) {
            $currentPath .= \DIRECTORY_SEPARATOR . $segment;
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

    private function zipEntryDestination(string $destination, string $name): string
    {
        $entryPath = str_replace('/', \DIRECTORY_SEPARATOR, trim($name, '/'));
        return $destination . \DIRECTORY_SEPARATOR . $entryPath;
    }

    private function isPathInside(string $basePath, string $targetPath): bool
    {
        $base = $this->normalizeFilesystemPath($basePath);
        $target = $this->normalizeFilesystemPath($targetPath);

        if (\DIRECTORY_SEPARATOR === '\\') {
            $base = strtolower($base);
            $target = strtolower($target);
        }

        return $target === $base || str_starts_with($target, $base . '/');
    }

    private function normalizeFilesystemPath(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);
        $normalized = preg_replace('#/+#', '/', $normalized) ?? $normalized;
        return rtrim($normalized, '/');
    }

    private function isZipEntrySymlink(\ZipArchive $zip, int $index): bool
    {
        $opsys = 0;
        $attributes = 0;
        if (! $zip->getExternalAttributesIndex($index, $opsys, $attributes)) {
            return false;
        }

        return (($attributes >> 16) & 0xF000) === self::ZIP_UNIX_SYMLINK_MODE;
    }

    protected function throwParamsFail()
    {
        throw new BusinessException(ResultCode::FAIL, trans('app-store.params_fail'));
    }

    protected function throwDownloadFail()
    {
        throw new BusinessException(ResultCode::FAIL, trans('app-store.download_fail'));
    }

    protected function throwAppInstalled()
    {
        throw new BusinessException(ResultCode::FAIL, trans('app-store.app_installed'));
    }

    protected function throwAppNoInstall()
    {
        throw new BusinessException(ResultCode::FAIL, trans('app-store.app_not_installed'));
    }
}
