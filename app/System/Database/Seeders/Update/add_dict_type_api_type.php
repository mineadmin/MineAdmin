<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AddDictTypeApiType extends Seeder
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

        $sql = "INSERT INTO `{$tableName}`(`id`, `name`, `code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8619580222112, '接口数据类型', 'api_data_type', '0', NULL, NULL, '2021-11-22 20:56:03', '2021-11-22 20:56:03', NULL, NULL)";

        try {
            Db::beginTransaction();
            Db::insert($sql);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }
}
