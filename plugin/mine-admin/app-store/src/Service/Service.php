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
        try {
            $runtimePath = BASE_PATH . '/runtime/' . uniqid('mineApp', true) . '.zip';
            $file->moveTo($runtimePath);
            $zip = new \ZipArchive();
            $zip->open($runtimePath);
            if ($zip->status !== \ZipArchive::ER_OK) {
                throw new \RuntimeException('Failed to open the zip file');
            }
            $this->assertSafeZipEntries($zip);
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
            $zip->extractTo(Plugin::PLUGIN_PATH . '/' . $identifier);
            $zip->close();
            Plugin::forceRefreshJsonPath();
            Plugin::install($identifier);
            @unlink($runtimePath);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
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

    private function assertSafeZipEntries(\ZipArchive $zip): void
    {
        for ($index = 0; $index < $zip->numFiles; ++$index) {
            $name = $zip->getNameIndex($index);
            if (
                ! is_string($name)
                || $this->isUnsafeZipEntryName($name)
                || $this->isZipEntrySymlink($zip, $index)
            ) {
                throw new \RuntimeException('Invalid zip entry path');
            }
        }
    }

    private function isUnsafeZipEntryName(string $name): bool
    {
        if ($name === '' || str_contains($name, "\0")) {
            return true;
        }

        $normalized = str_replace('\\', '/', $name);
        if ($normalized[0] === '/' || preg_match('/\A[A-Za-z]:/', $normalized)) {
            return true;
        }

        foreach (explode('/', $normalized) as $segment) {
            if ($segment === '..') {
                return true;
            }
        }

        return false;
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
