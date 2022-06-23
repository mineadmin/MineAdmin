<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemUploadFileMapper;
use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Utils\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\MineUpload;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * 文件上传业务
 * Class SystemLoginLogService
 * @package App\System\Service
 */
class SystemUploadFileService extends AbstractService
{
    /**
     * @var ConfigInterface
     */
    #[Inject]
    protected $config;

    /**
     * @var SystemUploadFileMapper
     */
    public $mapper;

    /**
     * @var MineUpload
     */
    protected MineUpload $mineUpload;


    public function __construct(SystemUploadFileMapper $mapper, MineUpload $mineUpload)
    {
        $this->mapper = $mapper;
        $this->mineUpload = $mineUpload;
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
        try {
            $hash = md5_file($uploadedFile->getPath() . '/' . $uploadedFile->getFilename());
            if ($model = $this->mapper->getFileInfoByHash($hash)) {
                return $model->toArray();
            }
        } catch (Exception $e) {
            throw new NormalStatusException('获取文件Hash失败', 500);
        }
        $data = $this->mineUpload->upload($uploadedFile, $config);
        if ($this->save($data)) {
            return $data;
        } else {
            return [];
        }
    }

    /**
     * 获取当前目录下所有文件（包含目录）
     * @param array $params
     * @return array
     */
    public function getAllFile(array $params = []): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 数组数据搜索器
     * @param Collection $collect
     * @param array $params
     * @return Collection
     */
    protected function handleArraySearch(Collection $collect, array $params): Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row['name'], $params['name']);
            });
        }

        if ($params['label'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row['label'], $params['label']);
            });
        }
        return $collect;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        $directory = $this->getDirectory($params['storage_path'] ?? '');

        $params['select'] = [
            'id',
            'origin_name',
            'object_name',
            'mime_type',
            'url',
            'size_info',
            'storage_path',
            'created_at'
        ];

        $params['select'] = implode(',', $params['select']);

        return array_merge($directory, $this->getList($params));
    }

    /**
     * 保存网络图片
     * @param array $data ['url', 'path']
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function saveNetworkImage(array $data): array
    {
        $data = $this->mineUpload->handleSaveNetworkImage($data);
        if ($this->save($data)) {
            return $data;
        } else {
            return [];
        }
    }
}
