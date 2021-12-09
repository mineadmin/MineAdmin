<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AddDictDataRequestModeData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemDictData::getModel()->getTable();

        $sql = [
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259153751712, 8259136986784, '所有', 'A', 'request_mode', 3, '0', NULL, NULL, '2021-11-14 17:23:25', '2021-11-14 17:23:25', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259160266912, 8259136986784, 'POST', 'P', 'request_mode', 2, '0', NULL, NULL, '2021-11-14 17:23:38', '2021-11-14 17:23:38', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259163719840, 8259136986784, 'GET', 'G', 'request_mode', 1, '0', NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
        ];
        try {
            Db::beginTransaction();
            foreach ($sql as $str) {
                Db::insert($str);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }
}
