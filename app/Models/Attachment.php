<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id 主键
 * @property string $storage_mode 存储模式:local=本地,oss=阿里云,qiniu=七牛云,cos=腾讯云
 * @property string|null $origin_name 原文件名
 * @property string|null $object_name 新文件名
 * @property string|null $hash 文件hash
 * @property string|null $mime_type 资源类型
 * @property string|null $storage_path 存储目录
 * @property string|null $suffix 文件后缀
 * @property int|null $size_byte 字节数
 * @property string|null $size_info 文件大小
 * @property string|null $url url地址
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property string $remark 备注
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereObjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereOriginName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereSizeByte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereSizeInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereStorageMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereStoragePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereUrl($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'storage_mode', 'origin_name', 'object_name', 'hash', 'mime_type', 'storage_path', 'suffix', 'size_byte', 'size_info', 'url', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'])]
final class Attachment extends Model
{
    protected $table = 'attachment';

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'size_byte' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
