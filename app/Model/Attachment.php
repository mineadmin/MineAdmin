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

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $id 主键
 * @property int $storage_mode 存储模式 (1 本地 2 阿里云 3 七牛云 4 腾讯云)
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
 * @property string $remark 备注
 */
final class Attachment extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'attachment';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'storage_mode', 'origin_name', 'object_name', 'hash', 'mime_type', 'storage_path', 'suffix', 'size_byte', 'size_info', 'url', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'storage_mode' => 'integer', 'size_byte' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
