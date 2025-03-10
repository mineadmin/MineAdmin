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
            'meta'  =>  [
                'title' => '部门管理',
                'icon' => 'ri:git-repository-private-line',
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
            'permission:department:index' => '部门列表',
            'permission:department:save'    =>  '部门新增',
            'permission:department:update'  =>  '部门编辑',
            'permission:department:delete'  =>  '部门删除',
        ];
        foreach ($children as $child => $title) {
            Menu::create([
                'name'  =>  $child,
                'path'  =>  '/permission/departments',
                'meta'  =>  [
                    'title' => $title,
                    'icon' => 'ri:git-repository-private-line',
                    'type' => 'M',
                    'hidden' => 1,
                    'componentPath' => 'modules/',
                    'componentSuffix' => '.vue',
                    'breadcrumbEnable' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ],
                'parent_id' => $now->id
            ]);
        }
    }
}
