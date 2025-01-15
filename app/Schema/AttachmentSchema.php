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

namespace App\Schema;

use App\Model\Attachment;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'AttachmentSchema')]
final class AttachmentSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'storage_mode', title: '存储模式 (1 本地 2 阿里云 3 七牛云 4 腾讯云)', type: 'int')]
    public ?int $storageMode;

    #[Property(property: 'origin_name', title: '原文件名', type: 'string')]
    public ?string $originName;

    #[Property(property: 'object_name', title: '新文件名', type: 'string')]
    public ?string $objectName;

    #[Property(property: 'hash', title: '文件hash', type: 'string')]
    public ?string $hash;

    #[Property(property: 'mime_type', title: '资源类型', type: 'string')]
    public ?string $mimeType;

    #[Property(property: 'storage_path', title: '存储目录', type: 'string')]
    public ?string $storagePath;

    #[Property(property: 'suffix', title: '文件后缀', type: 'string')]
    public ?string $suffix;

    #[Property(property: 'size_byte', title: '字节数', type: 'int')]
    public ?int $sizeByte;

    #[Property(property: 'size_info', title: '文件大小', type: 'string')]
    public ?string $sizeInfo;

    #[Property(property: 'url', title: 'url地址', type: 'string')]
    public ?string $url;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '', type: 'mixed')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '', type: 'mixed')]
    public mixed $updatedAt;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function __construct(Attachment $model)
    {
        $this->id = $model->id;
        $this->storageMode = $model->storage_mode;
        $this->originName = $model->origin_name;
        $this->objectName = $model->object_name;
        $this->hash = $model->hash;
        $this->mimeType = $model->mime_type;
        $this->storagePath = $model->storage_path;
        $this->suffix = $model->suffix;
        $this->sizeByte = $model->size_byte;
        $this->sizeInfo = $model->size_info;
        $this->url = $model->url;
        $this->createdBy = $model->created_by;
        $this->updatedBy = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'storage_mode' => $this->storageMode, 'origin_name' => $this->originName, 'object_name' => $this->objectName, 'hash' => $this->hash, 'mime_type' => $this->mimeType, 'storage_path' => $this->storagePath, 'suffix' => $this->suffix, 'size_byte' => $this->sizeByte, 'size_info' => $this->sizeInfo, 'url' => $this->url, 'created_by' => $this->createdBy, 'updated_by' => $this->updatedBy, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt, 'remark' => $this->remark];
    }
}
