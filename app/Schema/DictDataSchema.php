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

use App\Model\Setting\DictData;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'DictDataSchema')]
class DictDataSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'type_id', title: '字典类型ID', type: 'int')]
    public ?int $typeId;

    #[Property(property: 'label', title: '字典标签', type: 'string')]
    public ?string $label;

    #[Property(property: 'value', title: '字典值', type: 'string')]
    public ?string $value;

    #[Property(property: 'code', title: '字典标示', type: 'string')]
    public ?string $code;

    #[Property(property: 'sort', title: '排序', type: 'int')]
    public ?int $sort;

    #[Property(property: 'status', title: '状态 (1正常 2停用)', type: 'int')]
    public ?int $status;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '', type: 'mixed')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '', type: 'mixed')]
    public mixed $updatedAt;

    #[Property(property: 'deleted_at', title: '', type: 'mixed')]
    public mixed $deletedAt;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function __construct(DictData $model)
    {
        $this->id = $model->id;
        $this->typeId = $model->type_id;
        $this->label = $model->label;
        $this->value = $model->value;
        $this->code = $model->code;
        $this->sort = $model->sort;
        $this->status = $model->status;
        $this->createdBy = $model->created_by;
        $this->updatedBy = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
        $this->deletedAt = $model->deleted_at;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'type_id' => $this->typeId, 'label' => $this->label, 'value' => $this->value, 'code' => $this->code, 'sort' => $this->sort, 'status' => $this->status, 'created_by' => $this->createdBy, 'updated_by' => $this->updatedBy, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt, 'deleted_at' => $this->deletedAt, 'remark' => $this->remark];
    }
}
