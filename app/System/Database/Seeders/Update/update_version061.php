<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class UpdateVersion061 extends Seeder
{
    /**
     * Run the database seeds.
     * 0.6.1版本升级SQL
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {
            Db::beginTransaction();
            foreach ($this->getDictType() as $dType) {
                Db::insert($dType);
            }
            foreach ($this->getDictData() as $dict) {
                Db::insert($dict);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }

    protected function getDictType(): array
    {
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemDictType::getModel()->getTable();
        return [
            "INSERT INTO `{$tableName}`(`id`, `name`, `code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074866789575, '附件类型', 'attachment_type', '0', NULL, NULL, '2022-03-17 14:49:23', '2022-03-17 14:49:23', NULL, NULL)"
        ];
    }

    // 字典数据
    protected function getDictData(): array
    {
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemDictData::getModel()->getTable();
        Db::delete('DELETE FROM ' . $tableName . ' WHERE code = ?', [ 'request_mode' ]);
        return [
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259153751712, 8259136986784, '所有', 'A', 'request_mode', 5, '0', NULL, NULL, '2021-11-14 17:23:25', '2021-11-14 17:23:25', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259163719840, 8259136986784, 'GET', 'G', 'request_mode', 4, '0', NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259160266912, 8259136986784, 'POST', 'P', 'request_mode', 3, '0', NULL, NULL, '2021-11-14 17:23:38', '2021-11-14 17:23:38', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259163719842, 8259136986784, 'PUT', 'U', 'request_mode', 2, '0', NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8259163719843, 8259136986784, 'DELETE', 'D', 'request_mode', 1, '0', NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",

            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10695566593184, 10774866789575, '图片', 'image', 'attachment_type', 10, '0', NULL, NULL, '2022-03-17 14:49:59', '2022-03-17 14:49:59', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10695577459872, 10774866789575, '文档', 'text', 'attachment_type', 9, '0', NULL, NULL, '2022-03-17 14:50:20', '2022-03-17 14:50:49', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10695585910944, 10774866789575, '音频', 'audio', 'attachment_type', 8, '0', NULL, NULL, '2022-03-17 14:50:37', '2022-03-17 14:50:52', NULL, NULL)",
            "INSERT INTO `{$tableName}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10695590140064, 10774866789575, '视频', 'video', 'attachment_type', 7, '0', NULL, NULL, '2022-03-17 14:50:45', '2022-03-17 14:50:57', NULL, NULL)",
        ];
    }
}
