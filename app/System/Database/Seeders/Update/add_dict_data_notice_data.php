<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AddDictDataNoticeData extends Seeder
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
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8126628581024, 8126617434272, '通知', '1', 'backend_notice_type', 2, '0', NULL, NULL, '2021-11-11 17:29:27', '2021-11-11 17:30:51', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8126697847456, 8126617434272, '公告', '2', 'backend_notice_type', 1, '0', NULL, NULL, '2021-11-11 17:31:42', '2021-11-11 17:31:42', NULL, NULL)",
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
