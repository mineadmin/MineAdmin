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

namespace App\System\Mapper;

use App\System\Model\SystemUploadfile;
use Hyperf\Database\Model\Builder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Mine\Abstracts\AbstractMapper;
use Mine\Event\RealDeleteUploadFile;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class SystemUserMapper.
 */
class SystemUploadFileMapper extends AbstractMapper
{
    /**
     * @var SystemUploadfile
     */
    public $model;

    #[Inject]
    protected EventDispatcherInterface $evDispatcher;

    #[Inject]
    protected ContainerInterface $container;

    public function assignModel()
    {
        $this->model = SystemUploadfile::class;
    }

    /**
     * 通过hash获取上传文件的信息.
     * @return null|Builder|Model|object
     */
    public function getFileInfoByHash(string $hash, array $columns = ['*'])
    {
        $model = $this->model::query()->where('hash', $hash)->first($columns);
        if (! $model) {
            $model = $this->model::withTrashed()->where('hash', $hash)->first(['id']);
            $model && $model->forceDelete();
            return null;
        }
        return $model;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['storage_mode']) && filled($params['storage_mode'])) {
            $query->where('storage_mode', $params['storage_mode']);
        }
        if (isset($params['origin_name']) && filled($params['origin_name'])) {
            $query->where('origin_name', 'like', '%' . $params['origin_name'] . '%');
        }
        if (isset($params['storage_path']) && filled($params['storage_path'])) {
            $query->where('storage_path', 'like', $params['storage_path'] . '%');
        }
        if (isset($params['mime_type']) && filled($params['mime_type'])) {
            $query->where('mime_type', 'like', $params['mime_type'] . '/%');
        }
        if (isset($params['minDate']) && filled($params['minDate']) && isset($params['maxDate']) && filled($params['maxDate'])) {
            $query->whereBetween(
                'created_at',
                [$params['minDate'] . ' 00:00:00', $params['maxDate'] . ' 23:59:59']
            );
        }
        return $query;
    }

    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $storageMode = match ((string) $model->storage_mode) {
                    '1' => 'local',
                    '2' => 'oss',
                    '3' => 'qiniu',
                    '4' => 'cos',
                    '5' => 'ftp',
                    '6' => 'memory',
                    '7' => 's3',
                    '8' => 'minio',
                    default => 'local',
                };
                $event = new RealDeleteUploadFile(
                    $model,
                    $this->container->get(FilesystemFactory::class)->get($storageMode)
                );
                $this->evDispatcher->dispatch($event);
                if ($event->getConfirm()) {
                    $model->forceDelete();
                }
            }
        }
        unset($event);
        return true;
    }

    /**
     * 检查数据库中是否存在该目录数据.
     */
    public function checkDirDbExists(string $path): bool
    {
        return $this->model::withTrashed()
            ->where('storage_path', $path)
            ->orWhere('storage_path', 'like', $path . '/%')
            ->exists();
    }
}
