<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AddDictDataApiData extends Seeder
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
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645168257696, 8619580222112, 'String', '1', 'api_data_type', 7, '0', NULL, NULL, '2021-11-23 10:49:00', '2021-11-23 10:49:00', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645183473312, 8619580222112, 'Integer', '2', 'api_data_type', 6, '0', NULL, NULL, '2021-11-23 10:49:29', '2021-11-23 10:49:29', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645187911328, 8619580222112, 'Array', '3', 'api_data_type', 5, '0', NULL, NULL, '2021-11-23 10:49:38', '2021-11-23 10:49:38', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645192008352, 8619580222112, 'Float', '4', 'api_data_type', 4, '0', NULL, NULL, '2021-11-23 10:49:46', '2021-11-23 10:49:46', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645196093088, 8619580222112, 'Boolean', '5', 'api_data_type', 3, '0', NULL, NULL, '2021-11-23 10:49:54', '2021-11-23 10:49:54', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645207885984, 8619580222112, 'Enum', '6', 'api_data_type', 2, '0', NULL, NULL, '2021-11-23 10:50:17', '2021-11-23 10:50:17', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645212278944, 8619580222112, 'Object', '7', 'api_data_type', 1, '0', NULL, NULL, '2021-11-23 10:50:26', '2021-11-23 10:50:26', NULL, NULL)",
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
