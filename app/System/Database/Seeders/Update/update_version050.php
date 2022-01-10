<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class UpdateVersion050 extends Seeder
{
    /**
     * Run the database seeds.
     * 0.5.0版本升级SQL
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

            foreach ($this->getDictType() as $item) {
                Db::insert($item);
            }

            foreach ($this->getDictData() as $item) {
                Db::insert($item);
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
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (3850, 3300, '0,3000,3300', '队列日志', 'system:queueLog', 'el-icon-guide', 'queueLog', 'system/queueLog/index', NULL, '1', 'M', '0', 0, NULL, NULL, '2021-12-25 16:41:14', '2021-12-25 16:41:14', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (3851, 3850, '0,3000,3300,3850', '删除队列日志', 'system:queueLog:delete', '', NULL, '', NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 16:42:42', '2021-12-25 16:42:42', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (3852, 3850, '0,3000,3300,3850', '更新队列状态', 'system:queueLog:updateStatus', '', NULL, '', NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 16:45:03', '2021-12-25 16:47:16', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2700, 2000, '0,2000', '系统公告', 'system:notice', 'el-icon-flag', 'notice', 'system/notice/index', NULL, '1', 'M', '0', 94, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2701, 2750, '0,2000,2750', '系统公告列表', 'system:notice:index', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2702, 2750, '0,2000,2750', '系统公告回收站', 'system:notice:recycle', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2703, 2750, '0,2000,2750', '系统公告保存', 'system:notice:save', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2704, 2750, '0,2000,2750', '系统公告更新', 'system:notice:update', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2705, 2750, '0,2000,2750', '系统公告删除', 'system:notice:delete', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2706, 2750, '0,2000,2750', '系统公告读取', 'system:notice:read', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2707, 2750, '0,2000,2750', '系统公告恢复', 'system:notice:recovery', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2708, 2750, '0,2000,2750', '系统公告真实删除', 'system:notice:realDelete', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2709, 2750, '0,2000,2750', '系统公告导入', 'system:notice:import', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `parent_id`, `level`, `name`, `code`, `icon`, `route`, `component`, `redirect`, `is_hidden`, `type`, `status`, `sort`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (2710, 2750, '0,2000,2750', '系统公告导出', 'system:notice:export', NULL, NULL, NULL, NULL, '1', 'B', '0', 0, NULL, NULL, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL, NULL)",

        ];
    }

    // 字典类型
    protected function getDictType(): array
    {
        $table = env('DB_PREFIX') . \App\System\Model\SystemDictType::getModel()->getTable();
        return [
            "INSERT INTO `{$table}`(`id`, `name`, `code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074681620640, '队列生产状态', 'queue_produce_status', '0', NULL, NULL, '2021-12-25 18:22:38', '2021-12-25 18:22:38', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `name`, `code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074702532768, '队列消费状态', 'queue_consume_status', '0', NULL, NULL, '2021-12-25 18:23:19', '2021-12-25 18:23:19', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `name`, `code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074866778272, '队列消息类型', 'queue_msg_type', '0', NULL, NULL, '2021-12-25 18:28:40', '2021-12-25 18:28:40', NULL, NULL)",
        ];
    }

    // 字典数据
    protected function getDictData(): array
    {
        $table = env('DB_PREFIX') . \App\System\Model\SystemDictData::getModel()->getTable();
        return [
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (8645212278946, 8619580222112, 'File', '8', 'api_data_type', 0, '0', NULL, NULL, '2021-12-25 18:32:48', '2021-12-25 18:32:48', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074768599200, 10074681620640, '未生产', '0', 'queue_produce_status', 5, '0', NULL, NULL, '2021-12-25 18:25:28', '2021-12-25 18:25:28', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074773797536, 10074681620640, '生产中', '1', 'queue_produce_status', 4, '0', NULL, NULL, '2021-12-25 18:25:38', '2021-12-25 18:25:38', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074780033696, 10074681620640, '生产成功', '2', 'queue_produce_status', 3, '0', NULL, NULL, '2021-12-25 18:25:50', '2021-12-25 18:25:50', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074792360608, 10074681620640, '生产失败', '3', 'queue_produce_status', 2, '0', NULL, NULL, '2021-12-25 18:26:14', '2021-12-25 18:26:14', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074800282272, 10074681620640, '生产重复', '4', 'queue_produce_status', 1, '0', NULL, NULL, '2021-12-25 18:26:30', '2021-12-25 18:26:30', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074814135968, 10074702532768, '未消费', '0', 'queue_consume_status', 5, '0', NULL, NULL, '2021-12-25 18:26:57', '2021-12-25 18:26:57', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074819077792, 10074702532768, '消费中', '1', 'queue_consume_status', 4, '0', NULL, NULL, '2021-12-25 18:27:07', '2021-12-25 18:27:07', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074823951520, 10074702532768, '消费成功', '2', 'queue_consume_status', 3, '0', NULL, NULL, '2021-12-25 18:27:16', '2021-12-25 18:27:16', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074828146848, 10074702532768, '消费失败', '3', 'queue_consume_status', 2, '0', NULL, NULL, '2021-12-25 18:27:24', '2021-12-25 18:27:24', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074833623200, 10074702532768, '消费重复', '4', 'queue_consume_status', 1, '0', NULL, NULL, '2021-12-25 18:27:35', '2021-12-25 18:27:35', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074923844256, 10074866778272, '通知', 'notice', 'queue_msg_type', 1, '0', NULL, NULL, '2021-12-25 18:30:31', '2021-12-25 18:30:31', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074938784928, 10074866778272, '公告', 'announcement', 'queue_msg_type', 2, '0', NULL, NULL, '2021-12-25 18:31:00', '2021-12-25 18:31:00', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074951884448, 10074866778272, '待办', 'todo', 'queue_msg_type', 3, '0', NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074951884459, 10074866778272, '抄送我的', 'carbon_copy_mine', 'queue_msg_type', 4, '0', NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
            "INSERT INTO `{$table}`(`id`, `type_id`, `label`, `value`, `code`, `sort`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `remark`) VALUES (10074951884666, 10074866778272, '私信', 'private_message', 'queue_msg_type', 5, '0', NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",

        ];
    }
}
