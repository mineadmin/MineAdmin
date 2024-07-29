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
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

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
        Menu::truncate();
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
                'name' => '数据',
                'code' => 'dataCenter',
                'icon' => 'icon-storage',
                'route' => 'dataCenter',
                'is_hidden' => '2',
                'type' => 'M',
                'children' => [
                    [
                        'name' => '数据字典',
                        'code' => 'dataCenter:dict',
                        'icon' => 'ma-icon-dict',
                        'route' => 'dict',
                        'component' => 'dataCenter/dict/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '数据字典列表',
                                'code' => 'dataCenter:dict:index',
                            ],
                            [
                                'name' => '数据字典回收站',
                                'code' => 'dataCenter:dict:recycle',
                            ],
                            [
                                'name' => '数据字典保存',
                                'code' => 'dataCenter:dict:save',
                            ],
                            [
                                'name' => '数据字典更新',
                                'code' => 'dataCenter:dict:update',
                            ],
                            [
                                'name' => '数据字典删除',
                                'code' => 'dataCenter:dict:delete',
                            ],
                            [
                                'name' => '数据字典读取',
                                'code' => 'dataCenter:dict:read',
                            ],
                            [
                                'name' => '数据字典恢复',
                                'code' => 'dataCenter:dict:recovery',
                            ],
                            [
                                'name' => '数据字典真实删除',
                                'code' => 'dataCenter:dict:realDelete',
                            ],
                            [
                                'name' => '数据字典导入',
                                'code' => 'dataCenter:dict:import',
                            ],
                            [
                                'name' => '数据字典导出',
                                'code' => 'dataCenter:dict:export',
                            ],
                            [
                                'name' => '字典状态改变',
                                'code' => 'dataCenter:dict:changeStatus',
                            ],
                        ],
                    ],
                    [
                        'name' => '附件管理',
                        'code' => 'dataCenter:attachment',
                        'icon' => 'ma-icon-attach',
                        'route' => 'attachment',
                        'component' => 'dataCenter/attachment/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '附件列表',
                                'code' => 'dataCenter:attachment:index',
                            ],
                            [
                                'name' => '附件删除',
                                'code' => 'dataCenter:attachment:delete',
                            ],
                            [
                                'name' => '附件回收站',
                                'code' => 'dataCenter:attachment:recycle',
                            ],
                            [
                                'name' => '附件真实删除',
                                'code' => 'dataCenter:attachment:realDelete',
                            ],
                        ],
                    ],
                    [
                        'name' => '数据表维护',
                        'code' => 'dataCenter:dataMaintain',
                        'icon' => 'ma-icon-db',
                        'route' => 'dataMaintain',
                        'component' => 'dataCenter/dataMaintain/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '数据表列表',
                                'code' => 'dataCenter:dataMaintain:index',
                            ],
                            [
                                'name' => '数据表详细',
                                'code' => 'dataCenter:dataMaintain:detailed',
                            ],
                            [
                                'name' => '数据表清理碎片',
                                'code' => 'dataCenter:dataMaintain:fragment',
                            ],
                            [
                                'name' => '数据表优化',
                                'code' => 'dataCenter:dataMaintain:optimize',
                            ],
                        ],
                    ],
                    [
                        'name' => '系统公告',
                        'code' => 'dataCenter:notice',
                        'icon' => 'icon-bulb',
                        'route' => 'notice',
                        'component' => 'dataCenter/notice/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '系统公告列表',
                                'code' => 'dataCenter:notice:index',
                            ],
                            [
                                'name' => '系统公告回收站',
                                'code' => 'dataCenter:notice:recycle',
                            ],
                            [
                                'name' => '系统公告保存',
                                'code' => 'dataCenter:notice:save',
                            ],
                            [
                                'name' => '系统公告更新',
                                'code' => 'dataCenter:notice:update',
                            ],
                            [
                                'name' => '系统公告删除',
                                'code' => 'dataCenter:notice:delete',
                            ],
                            [
                                'name' => '系统公告读取',
                                'code' => 'dataCenter:notice:read',
                            ],
                            [
                                'name' => '系统公告恢复',
                                'code' => 'dataCenter:notice:recovery',
                            ],
                            [
                                'name' => '系统公告真实删除',
                                'code' => 'dataCenter:notice:realDelete',
                            ],
                            [
                                'name' => '系统公告导入',
                                'code' => 'dataCenter:notice:import',
                            ],
                            [
                                'name' => '系统公告导出',
                                'code' => 'dataCenter:notice:export',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => '监控',
                'code' => 'monitor',
                'icon' => 'icon-desktop',
                'route' => 'monitor',
                'is_hidden' => '2',
                'type' => 'M',
                'children' => [
                    [
                        'name' => '服务监控',
                        'code' => 'system:monitor:server',
                        'icon' => 'icon-thunderbolt',
                        'route' => 'server',
                        'component' => 'system/monitor/server/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                    ],
                    [
                        'name' => '缓存监控',
                        'code' => 'system:cache',
                        'icon' => 'icon-dice',
                        'route' => 'cache',
                        'component' => 'system/monitor/cache/index',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '获取Redis信息',
                                'code' => 'system:cache:monitor',
                            ],
                            [
                                'name' => '删除一个缓存',
                                'code' => 'system:cache:delete',
                            ],
                            [
                                'name' => '清空所有缓存',
                                'code' => 'system:cache:clear',
                            ],
                        ],
                    ],
                    [
                        'name' => '日志监控',
                        'code' => 'logs',
                        'icon' => 'icon-book',
                        'route' => 'logs',
                        'is_hidden' => '2',
                        'type' => 'M',
                        'children' => [
                            [
                                'name' => '登录日志',
                                'code' => 'system:loginLog',
                                'icon' => 'icon-idcard',
                                'route' => 'loginLog',
                                'component' => 'system/logs/loginLog',
                                'is_hidden' => '2',
                                'type' => 'M',
                            ],
                            [
                                'name' => '登录日志删除',
                                'code' => 'system:loginLog:delete',
                            ],
                            [
                                'name' => '操作日志',
                                'code' => 'system:operLog',
                                'icon' => 'icon-robot',
                                'route' => 'operLog',
                                'component' => 'system/logs/operLog',
                                'is_hidden' => '2',
                                'type' => 'M',
                            ],
                            [
                                'name' => '操作日志删除',
                                'code' => 'system:operLog:delete',
                            ],
                            [
                                'name' => '在线用户',
                                'code' => 'system:onlineUser',
                                'icon' => 'ma-icon-online',
                                'route' => 'onlineUser',
                                'component' => 'system/monitor/onlineUser/index',
                                'is_hidden' => '2',
                                'type' => 'M',
                            ],
                            [
                                'name' => '在线用户列表',
                                'code' => 'system:onlineUser:index',
                            ],
                            [
                                'name' => '强退用户',
                                'code' => 'system:onlineUser:kick',
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
            [
                'name' => '系统设置',
                'code' => 'setting:config',
                'icon' => 'icon-settings',
                'route' => 'system',
                'component' => 'setting/config/index',
                'is_hidden' => '2',
                'type' => 'M',
                'children' => [
                    [
                        'name' => '配置列表',
                        'code' => 'setting:config:index',
                    ],
                    [
                        'name' => '新增配置 ',
                        'code' => 'setting:config:save',
                    ],
                    [
                        'name' => '更新配置',
                        'code' => 'setting:config:update',
                    ],
                    [
                        'name' => '删除配置',
                        'code' => 'setting:config:delete',
                    ],
                    [
                        'name' => '清除配置缓存',
                        'code' => 'setting:config:clearCache',
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
