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

use App\Http\CurrentUser;
use App\Model\DataPermission\Policy;
use App\Model\Enums\DataPermission\PolicyType;
use App\Model\Permission\Department;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Schema\Schema;

class DataScope
{
    public static function query(Builder $builder, string $table)
    {
        // 不存在creatd_by字段，则不进行数据过滤
        if (!Schema::hasColumn($table, 'created_by')) {
            return $builder;
        }

        $currentUser = make(CurrentUser::class);
        try {
            $userId = $currentUser->id();
        } catch (\Throwable $e) {
            $userId = 0;
        }

        // 只有用户已登录时才应用条件
        if ($userId) {
            // 超级管理员拥有所有权限，不进行数据过滤
            if ($currentUser->isSuperAdmin()) {
                return $builder;
            }

            // 获取当前用户
            $user = $currentUser->user();

            // 首先获取用户自身的权限策略
            $userPolicies = Policy::where('user_id', $userId)->get();

            // 如果用户有自己的权限策略，则只使用用户的策略
            // 如果用户没有自己的权限策略，才查看角色权限
            $policies = null;
            if (!$userPolicies->isEmpty()) {
                $policies = $userPolicies->sortByDesc('is_default');
            } else {
                // 获取用户所有角色ID
                $roleIds = $user->roles()->pluck('role.id')->toArray();
                $rolePolicies = empty($roleIds) ? collect() : Policy::whereIn('role_id', $roleIds)->get();
                $policies = $rolePolicies->sortByDesc('is_default');
            }

            // 如果没有任何策略，默认使用"仅自己"权限
            if ($policies->isEmpty()) {
                return $builder->where("{$table}.created_by", $userId);
            }

            // 处理策略：如果任一策略是ALL，则不限制访问范围
            if ($policies->contains('policy_type', PolicyType::All)) {
                return $builder;
            }

            // 收集所有可访问的部门ID和用户ID
            $deptIds = collect();
            $createdByIds = collect([$userId]); // 默认自己的数据总是可访问的

            // 用户所有的部门ID（处理多部门情况）
            $userDeptIds = $user->department()->pluck('id')->toArray();

            // 处理每个策略
            foreach ($policies as $policy) {
                switch ($policy->policy_type) {
                    case PolicyType::Self:
                        // 仅自己 - 已包含在默认的createdByIds中
                        break;

                    case PolicyType::DeptSelf:
                        // 本部门数据 - 将用户所有部门添加到可访问部门列表
                        $deptIds = $deptIds->merge($userDeptIds);
                        break;

                    case PolicyType::DeptTree:
                        // 本部门及以下数据
                        if (!empty($userDeptIds)) {
                            $allDeptIds = [];
                            // 获取每个部门的子部门
                            foreach ($userDeptIds as $deptId) {
                                $allDeptIds[] = $deptId;
                                self::getChildDeptIds($deptId, $allDeptIds);
                            }
                            $deptIds = $deptIds->merge($allDeptIds);
                        }
                        break;

                    case PolicyType::CustomDept:
                        // 自定义部门
                        $customDeptIds = $policy->value['dept_ids'] ?? [];
                        if (!empty($customDeptIds)) {
                            $deptIds = $deptIds->merge($customDeptIds);
                        }
                        break;

                    case PolicyType::CustomFunc:
                        // 自定义函数
                        $funcName = $policy->value['func_name'] ?? '';
                        if (!empty($funcName) && function_exists($funcName)) {
                            // 这里需要特殊处理，因为自定义函数可能返回复杂的查询条件
                            // 临时保存在一个独立的变量里，后面再合并
                            $customQuery = (clone $builder);
                            $customQuery = call_user_func($funcName, $customQuery, $table, $userId, $user);

                            // TODO: 这里需要一个额外的机制来合并自定义查询的结果
                            // 由于无法直接合并查询条件，需要通过其他方式实现

                            // 这里是一个简单的处理方案，如果有自定义函数，我们暂时将其作为一个单独的Or条件
                            // 真实场景中可能需要更复杂的合并逻辑
                            return $builder->where(function ($query) use ($table, $createdByIds, $deptIds, $customQuery) {
                                // 应用其他范围条件
                                if (!$deptIds->isEmpty()) {
                                    $query->orWhereIn("{$table}.dept_id", $deptIds->unique()->values()->toArray());
                                }
                                if (!$createdByIds->isEmpty()) {
                                    $query->orWhereIn("{$table}.created_by", $createdByIds->unique()->values()->toArray());
                                }

                                // 添加自定义查询的条件（简化处理，实际可能需要更复杂的逻辑）
                                $query->orWhereRaw('EXISTS (' . $customQuery->toSql() . ')', $customQuery->getBindings());
                            });
                        }
                        break;
                }
            }

            // 根据收集到的部门ID和用户ID构建最终的查询条件
            return $builder->where(function ($query) use ($table, $createdByIds, $deptIds) {
                // 如果有部门ID，添加部门条件
                if (!$deptIds->isEmpty()) {
                    $query->orWhereIn("{$table}.dept_id", $deptIds->unique()->values()->toArray());
                }

                // 添加创建者条件
                if (!$createdByIds->isEmpty()) {
                    $query->orWhereIn("{$table}.created_by", $createdByIds->unique()->values()->toArray());
                }
            });
        }

        return $builder;
    }

    /**
     * 递归获取子部门ID
     */
    private static function getChildDeptIds(int $deptId, array &$deptIds): void
    {
        $childDepts = Department::where('parent_id', $deptId)->get();

        foreach ($childDepts as $dept) {
            $deptIds[] = $dept->id;
            self::getChildDeptIds($dept->id, $deptIds);
        }
    }
} 