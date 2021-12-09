<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AddDictTypeNoticeType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemDictType::getModel()->getTable();

        $sql = "INSERT INTO `{$tableName}`(`id`, `name`, `code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8126617434272, '后台公告类型', 'backend_notice_type', '0', NULL, NULL, '2021-11-11 17:29:05', '2021-11-11 17:29:14', NULL, NULL)";

        try {
            Db::beginTransaction();
            Db::insert($sql);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }
}
