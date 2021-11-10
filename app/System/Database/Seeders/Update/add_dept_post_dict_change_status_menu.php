<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AddDeptPostDictChangeStatusMenu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $table = env('DB_PREFIX') . \App\System\Model\SystemMenu::getModel()->getTable();
        $menus = [
            "INSERT INTO `{$table}` VALUES (1311, 1300, '0,1000,1300', '部门状态改变', 'system:dept:changeStatus', '', NULL, '', NULL, '1', 'B', '0', 0, NULL, NULL, '2021-11-09 18:26:15', '2021-11-09 18:26:15', NULL, NULL)",
            "INSERT INTO `{$table}` VALUES (1511, 1500, '0,1000,1500', '岗位状态改变', 'system:post:changeStatus', '', NULL, '', NULL, '1', 'B', '0', 0, NULL, NULL, '2021-11-09 18:26:15', '2021-11-09 18:26:15', NULL, NULL)",
            "INSERT INTO `{$table}` VALUES (2112, 2100, '0,2000,2100', '字典状态改变', 'system:dataDict:changeStatus', '', NULL, '', NULL, '1', 'B', '0', 0, NULL, NULL, '2021-11-09 18:26:15', '2021-11-09 18:26:15', NULL, NULL)"
        ];

        try {
            Db::beginTransaction();
            foreach ($menus as $menu) {
                Db::insert($menu);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }
}
