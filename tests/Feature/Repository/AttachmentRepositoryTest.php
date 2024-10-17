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

namespace HyperfTests\Feature\Repository;

use App\Model\Attachment;
use App\Repository\AttachmentRepository;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class AttachmentRepositoryTest extends AbstractTestRepository
{
    protected string $repositoryClass = AttachmentRepository::class;

    protected function getAttributes(): array
    {
        /*
         * @property int $id 主键
         * @property string $storage_mode 存储模式 (1 本地 2 阿里云 3 七牛云 4 腾讯云)
         * @property string $origin_name 原文件名
         * @property string $object_name 新文件名
         * @property string $hash 文件hash
         * @property string $mime_type 资源类型
         * @property string $storage_path 存储目录
         * @property string $suffix 文件后缀
         * @property int $size_byte 字节数
         * @property string $size_info 文件大小
         * @property string $url url地址
         * @property int $created_by 创建者
         * @property int $updated_by 更新者
         * @property Carbon $created_at 创建时间
         * @property Carbon $updated_at 更新时间
         * @property Carbon $deleted_at 删除时间
         * @property string $remark 备注
         */
        return [
            'storage_mode' => random_int(1, 4),
            'origin_name' => Str::random(),
            'object_name' => Str::random(),
            'hash' => Str::random(),
            'mime_type' => Str::random(),
            'storage_path' => Str::random(),
            'suffix' => Str::random(),
            'size_byte' => random_int(1, 100),
            'size_info' => Str::random(),
            'url' => Str::random(),
            'created_by' => random_int(1, 100),
            'updated_by' => random_int(1, 100),
        ];
    }

    /**
     * @param Attachment $model
     */
    protected function getSearchAttributes(Model $model, Collection $entityList): array
    {
        return [
            'suffix' => $model->suffix,
            'mime_type' => $model->mime_type,
            'storage_mode' => $model->storage_mode,
            'created_by' => $model->created_by,
            'updated_by' => $model->updated_by,
            'created_at' => [$model->created_at->startOfDay(), $model->created_at->endOfDay()],
            'updated_at' => [$model->updated_at->startOfDay(), $model->updated_at->endOfDay()],
            'url' => $model->url,
            'hash' => $model->hash,
            'object_name' => $model->object_name,
            'origin_name' => $model->origin_name,
            'storage_path' => $model->storage_path,
        ];
    }
}
