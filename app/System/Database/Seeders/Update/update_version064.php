<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class UpdateVersion064 extends Seeder
{
    /**
     * Run the database seeds.
     * 0.6.4版本升级SQL
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
        $model = \App\System\Model\SystemMenu::getModel();
        $tableName = env('DB_PREFIX') . $model->getTable();

        $model->find(4206)->forceDelete();
        return [
            "INSERT INTO `{$tableName}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (4206, 4200, '0,4000,4200', '更新业务表', 'setting:code:update', '', 'codeEdit', 'setting/code/edit', NULL, '0', 'M', '0', 0, 6798055202976, 6798055202976, '2021-07-31 19:43:12', '2022-04-15 16:21:32', NULL, NULL)"
       ];
    }
}
