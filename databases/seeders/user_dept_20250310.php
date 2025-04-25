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

class UserDept20250310 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $parent = Menu::where('name','permission')->firstOrFail();
        $now = Menu::create([
            'name'  =>  'permission:department',
            'path'  =>  '/permission/departments',
            'parent_id' => $parent->id,
            'component' => 'base/views/permission/department/index',
            'meta'  =>  [
                'title' => '部门管理',
                'icon' => 'mingcute:department-line',
                'i18n' => 'baseMenu.permission.department',
                'type' => 'M',
                'hidden' => 0,
                'componentPath' => 'modules/',
                'componentSuffix' => '.vue',
                'breadcrumbEnable' => 1,
                'copyright' => 1,
                'cache' => 1,
                'affix' => 0,
            ]
        ]);
        $children = [
            'permission:department:index'  => '部门列表',
            'permission:department:save'   =>  '部门新增',
            'permission:department:update' =>  '部门编辑',
            'permission:department:delete' =>  '部门删除',
            'permission:position:index'  => '岗位列表',
            'permission:position:save'   =>  '岗位新增',
            'permission:position:update' =>  '岗位编辑',
            'permission:position:delete' =>  '岗位删除',
            'permission:position:data_permission' =>  '设置岗位数据权限',
            'permission:leader:index'  => '部门领导列表',
            'permission:leader:save'   =>  '新增部门领导',
            'permission:leader:delete' =>  '部门领导移除',
        ];
        $i18n = [
            'baseMenu.permission.departmentList',
            'baseMenu.permission.departmentCreate',
            'baseMenu.permission.departmentSave',
            'baseMenu.permission.departmentDelete',
            'baseMenu.permission.positionList',
            'baseMenu.permission.positionCreate',
            'baseMenu.permission.positionSave',
            'baseMenu.permission.positionDelete',
            'baseMenu.permission.positionDataScope',
            'baseMenu.permission.leaderList',
            'baseMenu.permission.leaderCreate',
            'baseMenu.permission.leaderDelete',
        ];
        $i = 0;
        foreach ($children as $child => $title) {
            Menu::create([
                'name'  =>  $child,
                'path'  =>  '/permission/departments',
                'meta'  =>  [
                    'title' => $title,
                    'type' => 'B',
                    'i18n' => $i18n[$i],
                    'hidden' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ],
                'parent_id' => $now->id
            ]);
            $i++;
        }
    }
}
