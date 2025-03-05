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

use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'DepartmentSchema', description: '部门模型')]
class DepartmentSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'parent_id', title: '父ID', type: 'int')]
    public ?int $parentId;

    #[Property(property: 'name', title: '部门名称', type: 'string')]
    public ?string $name;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '', type: 'string')]
    public mixed $updatedAt;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    #[Property(property: 'positions', title: '职位', type: 'array')]
    public ?array $positions;

    #[Property(property: 'department_users', title: '部门用户', type: 'array', items: new Items(ref: '#/components/schemas/UserSchema'))]
    public ?array $departmentUsers;

    #[Property(property: 'leader', title: '领导', type: 'array', items: new Items(ref: '#/components/schemas/UserSchema'))]
    public ?array $leader;

    public function jsonSerialize(): mixed
    {
        return [];
    }
}
