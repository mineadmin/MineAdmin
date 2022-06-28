<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

namespace Mine;

use App\Setting\Service\SettingConfigService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use League\Flysystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Mine\Helper\Str;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Container\ContainerInterface;

class MineUpload
{
    /**
     * @var FilesystemFactory
     */
    #[Inject]
    protected FilesystemFactory $factory;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var EventDispatcherInterface
     */
    #[Inject]
    protected EventDispatcherInterface $evDispatcher;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * 存储配置信息
     * @var array
     */
    protected array $config;

    /**
     * MineUpload constructor.
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = config('file.storage');
        $this->filesystem = $this->factory->get($this->getStorageMode());
    }

    /**
     * 获取文件操作处理系统
     * @return Filesystem
     */
    public function getFileSystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 上传文件
     * @param UploadedFile $uploadedFile
     * @param array $config
     * @return array
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function upload(UploadedFile $uploadedFile, array $config = []): array
    {
        return $this->handleUpload($uploadedFile, $config);
    }

    /**
     * 处理上传
     * @param UploadedFile $uploadedFile
     * @param array $config
     * @return array
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function handleUpload(UploadedFile $uploadedFile, array $config): array
    {
        $tmpFile = $uploadedFile->getPath() . '/' . $uploadedFile->getFilename();
        $path = $this->getPath($config['path'] ?? null, $this->getMappingMode() !== 1);
        $filename = $this->getNewName() . '.' . Str::lower($uploadedFile->getExtension());

        if (!$this->filesystem->writeStream($path . '/' . $filename, $uploadedFile->getStream()->detach())) {
            throw new NormalStatusException((string) $uploadedFile->getError(), 500);
        }

        $fileInfo = [
            'storage_mode' => $this->getMappingMode(),
            'origin_name' => $uploadedFile->getClientFilename(),
            'object_name' => $filename,
            'mime_type' => $uploadedFile->getClientMediaType(),
            'storage_path' => $path,
            'hash' => md5_file($tmpFile),
            'suffix' => Str::lower($uploadedFile->getExtension()),
            'size_byte' => $uploadedFile->getSize(),
            'size_info' => format_size($uploadedFile->getSize() * 1024),
            'url' => $this->assembleUrl($config['path'] ?? null, $filename),
        ];

        $this->evDispatcher->dispatch(new \Mine\Event\UploadAfter($fileInfo));

        return $fileInfo;
    }

    /**
     * 处理分块上传
     * @param array $data
     * @return array
     */
    public function handleChunkUpload(array $data): array
    {
        $uploadFile = $data['package'];
        /* @var UploadedFile $uploadFile */
        $path = BASE_PATH . '/runtime/chunk/';
        $chunkName = "{$path}{$data['hash']}_{$data['total']}_{$data['index']}.chunk";
        $fs = container()->get(\Hyperf\Utils\Filesystem\Filesystem::class);
        $fs->isDirectory($path) || $fs->makeDirectory($path);
        $uploadFile->moveTo($chunkName);
        if ($data['index'] === $data['total']) {
            $content = '';
            for($i = 1; $i <= $data['total']; $i++) {
                $chunkFile = "{$path}{$data['hash']}_{$data['total']}_{$i}.chunk";
                if (! $fs->isFile($chunkFile)) {
                    return ['chunk' => $data['index'], 'code' => 500, 'status' => 'fail'];
                }
                $content .= $fs->get($chunkFile);
                $fs->delete($chunkFile);
            }
            $fileName = $this->getNewName().'.'.Str::lower($data['ext']);
            $storagePath = $this->getPath(null, $this->getMappingMode() !== 1);
            if (! $this->filesystem->write($storagePath.'/'.$fileName, $content)) {
                throw new NormalStatusException('分块上传失败', 500);
            }
            $fileInfo = [
                'storage_mode' => $this->getMappingMode(),
                'origin_name' => $data['name'],
                'object_name' => $fileName,
                'mime_type' => $data['type'],
                'storage_path' => $storagePath,
                'hash' => $data['hash'],
                'suffix' => $data['ext'],
                'size_byte' => $data['size'],
                'size_info' => format_size(((int) $data['size'] * 1024)),
                'url' => $this->assembleUrl(null, $fileName),
            ];

            $this->evDispatcher->dispatch(new \Mine\Event\UploadAfter($fileInfo));

            return $fileInfo;
        }
        return ['chunk' => $data['index'], 'code' => 201, 'status' => 'success'];
    }

    /**
     * 保存网络图片
     * @param array $data
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function handleSaveNetworkImage(array $data): array
    {
        $path = $this->getPath($data['path'] ?? null, $this->getMappingMode() !== 1);
        $filename = $this->getNewName() . '.jpg';

        try {
            $content = file_get_contents($data['url']);

            $handle = fopen($data['url'], 'rb');
            $meta = stream_get_meta_data($handle);
            fclose($handle);

            $dataInfo = $meta['wrapper_data']['headers'] ?? $meta['wrapper_data'];
            $size = 0;

            foreach ($dataInfo as $va) {
                if ( preg_match('/length/iU', $va) ) {
                    $ts = explode(':', $va);
                    $size = intval(trim(array_pop($ts)));
                    break;
                }
            }

            $realPath = BASE_PATH . '/runtime/' . $filename;
            $fs = container()->get(\Hyperf\Utils\Filesystem\Filesystem::class);
            $fs->put($realPath, $content);

            $hash = md5_file($realPath);
            $fs->delete($realPath);

            if (! $hash) {
                throw new \Exception(t('network_image_save_fail'));
            }

            if ($model = (new \App\System\Mapper\SystemUploadFileMapper)->getFileInfoByHash($hash)) {
                return $model->toArray();
            }

            if (!$this->filesystem->write($path . '/' . $filename, $content)) {
                throw new \Exception(t('network_image_save_fail'));
            }

        } catch (\Throwable $e) {
            throw new NormalStatusException($e->getMessage(), 500);
        }

        $fileInfo = [
            'storage_mode' => $this->getMappingMode(),
            'origin_name' => md5((string) time()).'.jpg',
            'object_name' => $filename,
            'mime_type' => 'image/jpg',
            'storage_path' => $path,
            'suffix' => 'jpg',
            'hash' => $hash,
            'size_byte' => $size,
            'size_info' => format_size($size * 1024),
            'url' => $this->assembleUrl($data['path'] ?? null, $filename),
        ];

        $this->evDispatcher->dispatch(new \Mine\Event\UploadAfter($fileInfo));

        return $fileInfo;
    }

    /**
     * @param string $config
     * @param false $isContainRoot
     * @return string
     */
    protected function getPath(?string $path = null, bool $isContainRoot = false): string
    {
        $uploadfile = $isContainRoot ? '/'.env('UPLOAD_PATH', 'uploadfile').'/' : '';
        return empty($path) ? $uploadfile . date('Ymd') : $uploadfile . $path;
    }

    /**
     * 创建目录
     * @param string $name
     * @return bool
     */
    public function createUploadDir(string $name): bool
    {
        return $this->filesystem->createDir($name);
    }

    /**
     * 获取目录内容
     * @param string $path
     * @return array
     */
    public function listContents(string $path = ''): array
    {
        return $this->filesystem->listContents($path);
    }

    /**
     * 获取目录
     * @param string $path
     * @param bool $isChildren
     * @return array
     */
    public function getDirectory(string $path, bool $isChildren): array
    {
        $contents = $this->filesystem->listContents($path, $isChildren);
        $dirs = [];
        foreach ($contents as $content) {
            if ($content['type'] == 'dir') {
                $dirs[] = $content;
            }
        }
        return $dirs;
    }

    /**
     * 组装url
     * @param string|null $path
     * @param string $filename
     * @return string
     */
    public function assembleUrl(?string $path, string $filename): string
    {
        return $this->getPath($path, true) . '/' . $filename;
    }

    /**
     * 获取存储方式
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getStorageMode(): string
    {
        return $this->container->get(SettingConfigService::class)->getConfigByKey('site_storage_mode')['value'] ?? 'local';
    }

    /**
     * 获取编码后的文件名
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getNewName(): string
    {
        return (string) container()->get(\Hyperf\Snowflake\IdGeneratorInterface::class)->generate();
    }

    /**
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getMappingMode(): int
    {
        return match ( $this->getStorageMode() ) {
            'local' => 1,
            'oss'   => 2,
            'qiniu' => 3,
            'cos'   => 4,
            default => 1,
        };
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getProtocol(): string
    {
        return $this->container->get(MineRequest::class)->getScheme();
    }
}