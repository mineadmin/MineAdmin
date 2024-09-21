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
use App\Model\Permission\Meta;
use App\Model\Permission\Role;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;
use Mine\Kernel\Casbin\Rule\Rule;

class MenuSeeder extends Seeder
{
    public const BASE_DATA = [
        'name' => '',
        'path' => '',
        'component' => '',
        'redirect' => '',
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
                'name' => 'permission',
                'path' => '/permission',
                'meta' => new Meta([
                    'title' => '权限管理',
                    'i18n' => 'menu.permission.index',
                    'icon' => 'simple-icons:authy',
                    'type' => 'M',
                    'hidden' => 0,
                ]),
                'children' => [
                    [
                        'name' => 'permission:user',
                        'path' => '/permission/user',
                        'component' => 'base/views/permission/user/index.vue',
                        'meta' => new Meta([
                            'type' => 'M',
                            'title' => '用户管理',
                            'i18n' => 'menu.permission.user',
                            'icon' => 'material-symbols:account-box',
                            'hidden' => 0,
                            'cache' => 1,
                        ]),
                        'children' => [
                            [
                                'name' => 'permission:user:index',
                                'meta' => new Meta([
                                    'title' => '用户列表',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:user:save',
                                'meta' => new Meta([
                                    'title' => '用户保存',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:user:update',
                                'meta' => new Meta([
                                    'title' => '用户更新',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:user:delete',
                                'meta' => new Meta([
                                    'title' => '用户删除',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'user:password',
                                'meta' => new Meta([
                                    'title' => '用户初始化密码',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'user:role',
                                'meta' => new Meta([
                                    'title' => '用户角色赋予',
                                    'type' => 'B',
                                ]),
                            ],
                        ],
                    ],
                    [
                        'name' => 'permission:menu',
                        'path' => '/permission/menu',
                        'component' => 'base/views/permission/menu/index.vue',
                        'meta' => new Meta([
                            'title' => '菜单管理',
                            'i18n' => 'menu.permission.menu',
                            'icon' => 'material-symbols:list-alt-rounded',
                            'hidden' => 0,
                            'type' => 'M',
                            'cache' => 1,
                        ]),
                        'children' => [
                            [
                                'name' => 'permission:menu:index',
                                'meta' => new Meta([
                                    'title' => '菜单列表',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:menu:save',
                                'meta' => new Meta([
                                    'title' => '菜单保存',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:menu:update',
                                'meta' => new Meta([
                                    'title' => '菜单更新',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:menu:delete',
                                'meta' => new Meta([
                                    'title' => '菜单删除',
                                    'type' => 'B',
                                ]),
                            ],
                        ],
                    ],
                    [
                        'name' => 'permission:role',
                        'path' => '/permission/role',
                        'component' => 'base/views/permission/role/index.vue',
                        'meta' => new Meta([
                            'title' => '角色管理',
                            'i18n' => 'menu.permission.role',
                            'icon' => 'material-symbols:supervisor-account',
                            'hidden' => 0,
                            'type' => 'M',
                            'cache' => 1,
                        ]),
                        'children' => [
                            [
                                'name' => 'permission:role:index',
                                'meta' => new Meta([
                                    'title' => '角色列表',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:role:save',
                                'meta' => new Meta([
                                    'title' => '角色保存',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:role:update',
                                'meta' => new Meta([
                                    'title' => '角色更新',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'permission:role:delete',
                                'meta' => new Meta([
                                    'title' => '角色删除',
                                    'type' => 'B',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
            // todo 代码生成器还没定下来，所以先注释掉
            /* [
                'name' => '工具',
                'code' => 'devTools',
                'icon' => 'ma-icon-tool',
                'path' => 'devTools',
                'hidden' => '2',
                'type' => 'M',
                'children' => [
                    [
                        'name' => '代码生成器',
                        'code' => 'setting:code',
                        'icon' => 'ma-icon-code',
                        'path' => 'code',
                        'component' => 'setting/code/index',
                        'hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '预览代码',
                                'code' => 'setting:code:preview',
                                'type' => 'B',
                            ],
                            [
                                'name' => '装载数据表',
                                'code' => 'setting:code:loadTable',
                                'type' => 'B',
                            ],
                            [
                                'name' => '删除业务表',
                                'code' => 'setting:code:delete',
                                'type' => 'B',
                            ],
                            [
                                'name' => '同步业务表',
                                'code' => 'setting:code:sync',
                                'type' => 'B',
                            ],
                            [
                                'name' => '生成代码',
                                'code' => 'setting:code:generate',
                                'type' => 'B',
                            ],
                        ],
                    ],
                    [
                        'name' => '数据源管理',
                        'code' => 'setting:datasource',
                        'icon' => 'icon-storage',
                        'path' => 'setting/datasource',
                        'component' => 'setting/datasource/index',
                        'hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '数据源管理列表',
                                'code' => 'setting:datasource:index',
                                'type' => 'B',
                            ],
                            [
                                'name' => '数据源管理保存',
                                'code' => 'setting:datasource:save',
                                'type' => 'B',
                            ],
                            [
                                'name' => '数据源管理更新',
                                'code' => 'setting:datasource:update',
                                'type' => 'B',
                            ],
                            [
                                'name' => '数据源管理读取',
                                'code' => 'setting:datasource:read',
                                'type' => 'B',
                            ],
                            [
                                'name' => '数据源管理删除',
                                'code' => 'setting:datasource:delete',
                                'type' => 'B',
                            ],
                            [
                                'name' => '数据源管理导入',
                                'code' => 'setting:datasource:import',
                                'type' => 'B',
                            ],
                            [
                                'name' => '数据源管理导出',
                                'code' => 'setting:datasource:export',
                                'type' => 'B',
                            ],
                        ],
                    ],
                    [
                        'name' => '系统接口',
                        'code' => 'systemInterface',
                        'icon' => 'icon-compass',
                        'path' => 'systemInterface',
                        'component' => 'setting/systemInterface/index',
                        'hidden' => '2',
                        'type' => 'M',
                    ],
                ],
            ],*/
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
