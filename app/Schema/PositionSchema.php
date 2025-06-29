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

use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'PositionSchema', description: '岗位模型')]
class PositionSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'name', title: '岗位名称', type: 'string')]
    public ?string $name;

    #[Property(property: 'dept_id', title: '部门ID', type: 'int')]
    public ?int $deptId;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '', type: 'string')]
    public mixed $updatedAt;

    #[Property(property: 'department', title: '所属部门', type: 'object', ref: '#/components/schemas/DepartmentSchema')]
    public ?object $department;

    public function jsonSerialize(): mixed
    {
        return [];
    }
}
