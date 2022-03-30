<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class UpdateVersion060 extends Seeder
{
    /**
     * Run the database seeds.
     * 0.6.0版本升级SQL
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {
            Db::beginTransaction();
            foreach ($this->getMenus() as $menu) {
                Db::insert($menu);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }

    // 菜单
    protected function getMenus(): array
    {
        $table = env('DB_PREFIX') . \App\System\Model\SystemMenu::getModel()->getTable();
        return [
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (4104, 4100, '0,4000,4100', '模块启停用', 'setting:module:status', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2022-02-13 18:10:20', '2022-02-13 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (4105, 4100, '0,4000,4100', '安装模块', 'setting:module:install', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2022-02-13 18:10:20', '2022-02-13 18:10:20', NULL, NULL)",
        ];
    }
}
