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

use App\Model\Permission\Dept;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'DeptSchema')]
class DeptSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'parent_id', title: '父ID', type: 'int')]
    public ?int $parentId;

    #[Property(property: 'name', title: '部门名称', type: 'string')]
    public ?string $name;

    #[Property(property: 'leader', title: '负责人', type: 'string')]
    public ?string $leader;

    #[Property(property: 'phone', title: '联系电话', type: 'string')]
    public ?string $phone;

    #[Property(property: 'status', title: '状态 (1正常 2停用)', type: 'int')]
    public ?int $status;

    #[Property(property: 'sort', title: '排序', type: 'int')]
    public ?int $sort;

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

    public function __construct(Dept $model)
    {
        $this->id = $model->id;
        $this->parentId = $model->parent_id;
        $this->name = $model->name;
        $this->leader = $model->leader;
        $this->phone = $model->phone;
        $this->status = $model->status;
        $this->sort = $model->sort;
        $this->createdBy = $model->created_by;
        $this->updatedBy = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
        $this->deletedAt = $model->deleted_at;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'parent_id' => $this->parentId, 'name' => $this->name, 'leader' => $this->leader, 'phone' => $this->phone, 'status' => $this->status, 'sort' => $this->sort, 'created_by' => $this->createdBy, 'updated_by' => $this->updatedBy, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt, 'deleted_at' => $this->deletedAt, 'remark' => $this->remark];
    }
}
