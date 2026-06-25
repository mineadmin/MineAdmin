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

#[Schema(title: 'LeaderSchema', description: '部门领导模型')]
class LeaderSchema implements \JsonSerializable
{
    #[Property(property: 'dept_id', title: '部门ID', type: 'int')]
    public ?int $deptId;

    #[Property(property: 'user_id', title: '部门ID', type: 'int')]
    public ?int $userId;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '创建时间', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '更新时间', type: 'string')]
    public mixed $updatedAt;

    #[Property(property: 'department', title: '部门信息', type: 'object', ref: '#/components/schemas/DepartmentSchema')]
    public ?object $department;

    #[Property(property: 'user', title: '用户信息', type: 'object', ref: '#/components/schemas/UserSchema')]
    public ?object $user;

    public function jsonSerialize(): mixed
    {
        return [];
    }
}
