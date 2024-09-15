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
use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;
use Mine\Kernel\Casbin\Rule\Rule;

class MenuSeeder extends Seeder
{
    public const BASE_DATA = [
        'name' => '',
        'code' => '',
        'icon' => '',
        'route' => '',
        'component' => '',
        'redirect' => '',
        'is_hidden' => '1',
        'type' => 'B',
        'created_by' => 0,
        'updated_by' => 0,
        'remark' => '',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        Rule::truncate();
        Menu::truncate();
        Role::truncate();
        Role::create([
            'name' => '超级管理员（创始人）',
            'code' => 'SuperAdmin',
            'data_scope' => 0,
            'sort' => 0,
            'created_by' => env('SUPER_ADMIN', 0),
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'remark' => '系统内置角色，不可删除',
        ]);
        if (env('DB_DRIVER') === 'odbc-sql-server') {
            Db::unprepared('SET IDENTITY_INSERT [' . Menu::getModel()->getTable() . '] ON;');
        }
        $this->create($this->data());
        if (env('DB_DRIVER') === 'odbc-sql-server') {
            Db::unprepared('SET IDENTITY_INSERT [' . Menu::getModel()->getTable() . '] OFF;');
        }
    }

    /**
     * Database seeds data.
     */
    public function data(): array
    {
        return [
            [
                'name' => '权限',
                'code' => 'permission',
                'icon' => 'ma-icon-permission',
                'route' => 'permission',
                'is_hidden' => '2',
                'type' => 'M',
                'children' => [
                    [
                        'name' => '用户管理',
                        'code' => 'permission:user',
                        'icon' => 'ma-icon-user',
                        'route' => 'user',
                        'component' => 'permission/user/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '用户列表',
                                'code' => 'permission:user:index',
                            ],
                            [
                                'name' => '用户回收站列表',
                                'code' => 'permission:user:recycle',
                            ],
                            [
                                'name' => '用户保存',
                                'code' => 'permission:user:save',
                            ],
                            [
                                'name' => '用户更新',
                                'code' => 'permission:user:update',
                            ],
                            [
                                'name' => '用户删除',
                                'code' => 'permission:user:delete',
                            ],
                            [
                                'name' => '用户读取',
                                'code' => 'permission:user:read',
                            ],
                            [
                                'name' => '用户恢复',
                                'code' => 'permission:user:recovery',
                            ],
                            [
                                'name' => '用户真实删除',
                                'code' => 'permission:user:realDelete',
                            ],
                            [
                                'name' => '用户导入',
                                'code' => 'permission:user:import',
                            ],
                            [
                                'name' => '用户导出',
                                'code' => 'permission:user:export',
                            ],
                            [
                                'name' => '用户状态改变',
                                'code' => 'permission:user:changeStatus',
                            ],
                            [
                                'name' => '用户初始化密码',
                                'code' => 'permission:user:initUserPassword',
                            ],
                            [
                                'name' => '更新用户缓存',
                                'code' => 'permission:user:cache',
                            ],
                            [
                                'name' => '设置用户首页',
                                'code' => 'permission:user:homePage',
                            ],
                        ],
                    ],
                    [
                        'name' => '菜单管理',
                        'code' => 'permission:menu',
                        'icon' => 'icon-menu',
                        'route' => 'menu',
                        'component' => 'permission/menu/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '菜单列表',
                                'code' => 'permission:menu:index',
                            ],
                            [
                                'name' => '菜单回收站',
                                'code' => 'permission:menu:recycle',
                            ],
                            [
                                'name' => '菜单保存',
                                'code' => 'permission:menu:save',
                            ],
                            [
                                'name' => '菜单更新',
                                'code' => 'permission:menu:update',
                            ],
                            [
                                'name' => '菜单删除',
                                'code' => 'permission:menu:delete',
                            ],
                            [
                                'name' => '菜单读取',
                                'code' => 'permission:menu:read',
                            ],
                            [
                                'name' => '菜单恢复',
                                'code' => 'permission:menu:recovery',
                            ],
                            [
                                'name' => '菜单真实删除',
                                'code' => 'permission:menu:realDelete',
                            ],
                            [
                                'name' => '菜单导入',
                                'code' => 'permission:menu:import',
                            ],
                            [
                                'name' => '菜单导出',
                                'code' => 'permission:menu:export',
                            ],
                        ],
                    ],
                    [
                        'name' => '部门管理',
                        'code' => 'permission:dept',
                        'icon' => 'ma-icon-dept',
                        'route' => 'dept',
                        'component' => 'permission/dept/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '部门列表',
                                'code' => 'permission:dept:index',
                            ],
                            [
                                'name' => '部门回收站',
                                'code' => 'permission:dept:recycle',
                            ],
                            [
                                'name' => '部门保存',
                                'code' => 'permission:dept:save',
                            ],
                            [
                                'name' => '部门更新',
                                'code' => 'permission:dept:update',
                            ],
                            [
                                'name' => '部门删除',
                                'code' => 'permission:dept:delete',
                            ],
                            [
                                'name' => '部门读取',
                                'code' => 'permission:dept:read',
                            ],
                            [
                                'name' => '部门恢复',
                                'code' => 'permission:dept:recovery',
                            ],
                            [
                                'name' => '部门真实删除',
                                'code' => 'permission:dept:realDelete',
                            ],
                            [
                                'name' => '部门导入',
                                'code' => 'permission:dept:import',
                            ],
                            [
                                'name' => '部门导出',
                                'code' => 'permission:dept:export',
                            ],
                            [
                                'name' => '部门状态改变',
                                'code' => 'permission:dept:changeStatus',
                            ],
                        ],
                    ],
                    [
                        'name' => '角色管理',
                        'code' => 'permission:role',
                        'icon' => 'ma-icon-role',
                        'route' => 'role',
                        'component' => 'permission/role/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '角色列表',
                                'code' => 'permission:role:index',
                            ],
                            [
                                'name' => '角色回收站',
                                'code' => 'permission:role:recycle',
                            ],
                            [
                                'name' => '角色保存',
                                'code' => 'permission:role:save',
                            ],
                            [
                                'name' => '角色更新',
                                'code' => 'permission:role:update',
                            ],
                            [
                                'name' => '角色删除',
                                'code' => 'permission:role:delete',
                            ],
                            [
                                'name' => '角色读取',
                                'code' => 'permission:role:read',
                            ],
                            [
                                'name' => '角色恢复',
                                'code' => 'permission:role:recovery',
                            ],
                            [
                                'name' => '角色真实删除',
                                'code' => 'permission:role:realDelete',
                            ],
                            [
                                'name' => '角色导入',
                                'code' => 'permission:role:import',
                            ],
                            [
                                'name' => '角色导出',
                                'code' => 'permission:role:export',
                            ],
                            [
                                'name' => '角色状态改变',
                                'code' => 'permission:role:changeStatus',
                            ],
                            [
                                'name' => '更新菜单权限',
                                'code' => 'permission:role:menuPermission',
                            ],
                            [
                                'name' => '更新数据权限',
                                'code' => 'permission:role:dataPermission',
                            ],
                        ],
                    ],
                    [
                        'name' => '岗位管理',
                        'code' => 'permission:post',
                        'icon' => 'ma-icon-post',
                        'route' => 'post',
                        'component' => 'permission/post/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            53 => [
                                'name' => '岗位列表',
                                'code' => 'permission:post:index',
                            ],
                            [
                                'name' => '岗位回收站',
                                'code' => 'permission:post:recycle',
                            ],
                            [
                                'name' => '岗位保存',
                                'code' => 'permission:post:save',
                            ],
                            [
                                'name' => '岗位更新',
                                'code' => 'permission:post:update',
                            ],
                            [
                                'name' => '岗位删除',
                                'code' => 'permission:post:delete',
                            ],
                            [
                                'name' => '岗位读取',
                                'code' => 'permission:post:read',
                            ],
                            [
                                'name' => '岗位恢复',
                                'code' => 'permission:post:recovery',
                            ],
                            [
                                'name' => '岗位真实删除',
                                'code' => 'permission:post:realDelete',
                            ],
                            [
                                'name' => '岗位导入',
                                'code' => 'permission:post:import',
                            ],
                            [
                                'name' => '岗位导出',
                                'code' => 'permission:post:export',
                            ],
                            [
                                'name' => '岗位状态改变',
                                'code' => 'permission:post:changeStatus',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => '工具',
                'code' => 'devTools',
                'icon' => 'ma-icon-tool',
                'route' => 'devTools',
                'is_hidden' => '2',
                'type' => 'M',
                'children' => [
                    [
                        'name' => '代码生成器',
                        'code' => 'setting:code',
                        'icon' => 'ma-icon-code',
                        'route' => 'code',
                        'component' => 'setting/code/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '预览代码',
                                'code' => 'setting:code:preview',
                            ],
                            [
                                'name' => '装载数据表',
                                'code' => 'setting:code:loadTable',
                            ],
                            [
                                'name' => '删除业务表',
                                'code' => 'setting:code:delete',
                            ],
                            [
                                'name' => '同步业务表',
                                'code' => 'setting:code:sync',
                            ],
                            [
                                'name' => '生成代码',
                                'code' => 'setting:code:generate',
                            ],
                        ],
                    ],
                    [
                        'name' => '数据源管理',
                        'code' => 'setting:datasource',
                        'icon' => 'icon-storage',
                        'route' => 'setting/datasource',
                        'component' => 'setting/datasource/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '数据源管理列表',
                                'code' => 'setting:datasource:index',
                            ],
                            [
                                'name' => '数据源管理保存',
                                'code' => 'setting:datasource:save',
                            ],
                            [
                                'name' => '数据源管理更新',
                                'code' => 'setting:datasource:update',
                            ],
                            [
                                'name' => '数据源管理读取',
                                'code' => 'setting:datasource:read',
                            ],
                            [
                                'name' => '数据源管理删除',
                                'code' => 'setting:datasource:delete',
                            ],
                            [
                                'name' => '数据源管理导入',
                                'code' => 'setting:datasource:import',
                            ],
                            [
                                'name' => '数据源管理导出',
                                'code' => 'setting:datasource:export',
                            ],
                        ],
                    ],
                    [
                        'name' => '系统接口',
                        'code' => 'systemInterface',
                        'icon' => 'icon-compass',
                        'route' => 'systemInterface',
                        'component' => 'setting/systemInterface/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                    ],
                ],
            ],
        ];
    }

    public function create(array $data, int $parent_id = 0): void
    {
        foreach ($data as $v) {
            $_v = $v;
            if (isset($v['children'])) {
                unset($_v['children']);
            }
            $_v['parent_id'] = $parent_id;
            $menu = Menu::create(array_merge(self::BASE_DATA, $_v));
            if (isset($v['children']) && count($v['children'])) {
                $this->create($v['children'], $menu->id);
            }
        }
    }
}
