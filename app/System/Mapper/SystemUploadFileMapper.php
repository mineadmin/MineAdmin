<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemLoginLog;
use App\System\Model\SystemUploadfile;
use Hyperf\Database\Model\Builder;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractMapper;
use Mine\MineUpload;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class SystemUserMapper
 * @package App\System\Mapper
 */
class SystemUploadFileMapper extends AbstractMapper
{
    /**
     * @var SystemLoginLog
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
     * 通过hash获取上传文件的信息
     * @param string $hash
     * @return Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getFileInfoByHash(string $hash)
    {
        return $this->model::query()->where('hash', $hash)->first();
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['storage_mode'])) {
            $query->where('storage_mode', $params['storage_mode']);
        }
        if (isset($params['origin_name'])) {
            $query->where('origin_name', 'like', '%'.$params['origin_name'].'%');
        }
        if (isset($params['storage_path'])) {
            $query->where('storage_path', 'like', $params['storage_path'].'%');
        }
        if (!empty($params['mime_type'])) {
            $query->where('mime_type', 'like', $params['mime_type'].'/%');
        }
        if (isset($params['minDate']) && isset($params['maxDate'])) {
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
                $event = new \Mine\Event\RealDeleteUploadFile(
                    $model, $this->container->get(MineUpload::class)->getFileSystem()
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
     * 检查数据库中是否存在该目录数据
     * @param string $path
     * @return bool
     */
    public function checkDirDbExists(string $path): bool
    {
        return $this->model::withTrashed()
                ->where('storage_path', $path)
                ->orWhere('storage_path', 'like', $path . '/%')
                ->exists();
    }
}