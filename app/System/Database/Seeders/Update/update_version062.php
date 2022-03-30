<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class UpdateVersion062 extends Seeder
{
    /**
     * Run the database seeds.
     * 0.6.2版本升级SQL
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {
            Db::beginTransaction();
            foreach ($this->getMenu() as $menu) {
                Db::insert($menu);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }

    protected function getMenu(): array
    {
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemMenu::getModel()->getTable();
        return [
            "INSERT INTO `{$tableName}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (4600, 4000, '0,4000', '系统接口', 'systemInterface', 'el-icon-histogram', 'systemInterface', 'setting/systemInterface/index', NULL, '1', 'M', '0', 0, NULL, NULL, '2022-03-30 10:00:28', '2022-03-30 10:00:28', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (4601, 4000, '0,4000', '表单设计器', 'formDesigner', 'el-icon-set-up', 'formDesigner', 'setting/formDesigner/index', NULL, '1', 'M', '0', 0, NULL, NULL, '2022-03-30 10:13:37', '2022-03-30 10:32:36', NULL, NULL)"
        ];
    }
}
