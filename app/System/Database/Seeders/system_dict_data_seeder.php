<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */
declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use App\System\Model\SystemDictData;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SystemDictDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Db::table('system_dict_data')->truncate();

        foreach ($this->getData() as $item) {
            Db::insert($item);
        }
    }

    protected function getData(): array
    {
        $tableName = env('DB_PREFIX') . SystemDictData::getModel()->getTable();
        if (env('DB_DRIVER') == 'pgsql') {
            Db::select("SELECT setval('{$tableName}_id_seq', 48)");
            return [
                "INSERT INTO \"{$tableName}\" VALUES (1, 1, 'InnoDB', 'InnoDB', 'table_engine', 0, 1, NULL, NULL, '2021-06-27 00:37:11', '2021-06-27 13:33:29', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (2, 1, 'MyISAM', 'MyISAM', 'table_engine', 0, 1, NULL, NULL, '2021-06-27 00:37:21', '2021-06-27 13:33:29', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (3, 2, '本地存储', '1', 'upload_mode', 99, 1, NULL, NULL, '2021-06-27 13:33:43', '2021-06-27 13:33:43', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (4, 2, '阿里云OSS', '2', 'upload_mode', 98, 1, NULL, NULL, '2021-06-27 13:33:55', '2021-06-27 13:33:55', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (5, 2, '七牛云', '3', 'upload_mode', 97, 1, NULL, NULL, '2021-06-27 13:34:07', '2021-06-27 13:34:07', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (6, 2, '腾讯云COS', '4', 'upload_mode', 96, 1, NULL, NULL, '2021-06-27 13:34:19', '2021-06-27 13:34:19', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (7, 3, '正常', '1', 'data_status', 0, 1, NULL, NULL, '2021-06-27 13:36:51', '2021-06-27 13:37:01', NULL, '0为正常')",
                "INSERT INTO \"{$tableName}\" VALUES (8, 3, '停用', '2', 'data_status', 0, 1, NULL, NULL, '2021-06-27 13:37:10', '2021-06-27 13:37:10', NULL, '1为停用')",
                "INSERT INTO \"{$tableName}\" VALUES (9, 4, '统计页面', 'statistics', 'dashboard', 0, 1, NULL, NULL, '2021-08-09 12:53:53', '2021-08-09 12:53:53', NULL, '管理员用')",
                "INSERT INTO \"{$tableName}\" VALUES (10, 4, '工作台', 'work', 'dashboard', 0, 1, NULL, NULL, '2021-08-09 12:54:18', '2021-08-09 12:54:18', NULL, '员工使用')",
                "INSERT INTO \"{$tableName}\" VALUES (11, 5, '男', '1', 'sex', 0, 1, NULL, NULL, '2021-08-09 12:55:00', '2021-08-09 12:55:00', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (12, 5, '女', '2', 'sex', 0, 1, NULL, NULL, '2021-08-09 12:55:08', '2021-08-09 12:55:08', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (13, 5, '未知', '3', 'sex', 0, 1, NULL, NULL, '2021-08-09 12:55:16', '2021-08-09 12:55:16', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (14, 6, 'String', '1', 'api_data_type', 7, 1, NULL, NULL, '2021-11-23 10:49:00', '2021-11-23 10:49:00', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (15, 6, 'Integer', '2', 'api_data_type', 6, 1, NULL, NULL, '2021-11-23 10:49:29', '2021-11-23 10:49:29', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (16, 6, 'Array', '3', 'api_data_type', 5, 1, NULL, NULL, '2021-11-23 10:49:38', '2021-11-23 10:49:38', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (17, 6, 'Float', '4', 'api_data_type', 4, 1, NULL, NULL, '2021-11-23 10:49:46', '2021-11-23 10:49:46', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (18, 6, 'Boolean', '5', 'api_data_type', 3, 1, NULL, NULL, '2021-11-23 10:49:54', '2021-11-23 10:49:54', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (19, 6, 'Enum', '6', 'api_data_type', 2, 1, NULL, NULL, '2021-11-23 10:50:17', '2021-11-23 10:50:17', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (20, 6, 'Object', '7', 'api_data_type', 1, 1, NULL, NULL, '2021-11-23 10:50:26', '2021-11-23 10:50:26', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (21, 6, 'File', '8', 'api_data_type', 0, 1, NULL, NULL, '2021-12-25 18:32:48', '2021-12-25 18:32:48', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (22, 7, '通知', '1', 'backend_notice_type', 2, 1, NULL, NULL, '2021-11-11 17:29:27', '2021-11-11 17:30:51', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (23, 7, '公告', '2', 'backend_notice_type', 1, 1, NULL, NULL, '2021-11-11 17:31:42', '2021-11-11 17:31:42', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (24, 8, '所有', 'A', 'request_mode', 5, 1, NULL, NULL, '2021-11-14 17:23:25', '2021-11-14 17:23:25', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (25, 8, 'GET', 'G', 'request_mode', 4, 1, NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (26, 8, 'POST', 'P', 'request_mode', 3, 1, NULL, NULL, '2021-11-14 17:23:38', '2021-11-14 17:23:38', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (27, 8, 'PUT', 'U', 'request_mode', 2, 1, NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (28, 8, 'DELETE', 'D', 'request_mode', 1, 1, NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (29, 9, '未生产', '1', 'queue_produce_status', 5, 1, NULL, NULL, '2021-12-25 18:25:28', '2021-12-25 18:25:28', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (30, 9, '生产中', '2', 'queue_produce_status', 4, 1, NULL, NULL, '2021-12-25 18:25:38', '2021-12-25 18:25:38', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (31, 9, '生产成功', '3', 'queue_produce_status', 3, 1, NULL, NULL, '2021-12-25 18:25:50', '2021-12-25 18:25:50', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (32, 9, '生产失败', '4', 'queue_produce_status', 2, 1, NULL, NULL, '2021-12-25 18:26:14', '2021-12-25 18:26:14', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (33, 9, '生产重复', '5', 'queue_produce_status', 1, 1, NULL, NULL, '2021-12-25 18:26:30', '2021-12-25 18:26:30', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (34, 10, '未消费', '1', 'queue_consume_status', 5, 1, NULL, NULL, '2021-12-25 18:26:57', '2021-12-25 18:26:57', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (35, 10, '消费中', '2', 'queue_consume_status', 4, 1, NULL, NULL, '2021-12-25 18:27:07', '2021-12-25 18:27:07', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (36, 10, '消费成功', '3', 'queue_consume_status', 3, 1, NULL, NULL, '2021-12-25 18:27:16', '2021-12-25 18:27:16', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (37, 10, '消费失败', '4', 'queue_consume_status', 2, 1, NULL, NULL, '2021-12-25 18:27:24', '2021-12-25 18:27:24', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (38, 10, '消费重复', '5', 'queue_consume_status', 1, 1, NULL, NULL, '2021-12-25 18:27:35', '2021-12-25 18:27:35', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (39, 11, '通知', 'notice', 'queue_msg_type', 1, 1, NULL, NULL, '2021-12-25 18:30:31', '2021-12-25 18:30:31', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (40, 11, '公告', 'announcement', 'queue_msg_type', 2, 1, NULL, NULL, '2021-12-25 18:31:00', '2021-12-25 18:31:00', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (41, 11, '待办', 'todo', 'queue_msg_type', 3, 1, NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (42, 11, '抄送我的', 'carbon_copy_mine', 'queue_msg_type', 4, 1, NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (43, 11, '私信', 'private_message', 'queue_msg_type', 5, 1, NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (44, 12, '图片', 'image', 'attachment_type', 10, 1, NULL, NULL, '2022-03-17 14:49:59', '2022-03-17 14:49:59', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (45, 12, '文档', 'text', 'attachment_type', 9, 1, NULL, NULL, '2022-03-17 14:50:20', '2022-03-17 14:50:49', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (46, 12, '音频', 'audio', 'attachment_type', 8, 1, NULL, NULL, '2022-03-17 14:50:37', '2022-03-17 14:50:52', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (47, 12, '视频', 'video', 'attachment_type', 7, 1, NULL, NULL, '2022-03-17 14:50:45', '2022-03-17 14:50:57', NULL, NULL)",
                "INSERT INTO \"{$tableName}\" VALUES (48, 12, '应用程序', 'application', 'attachment_type', 6, 1, NULL, NULL, '2022-03-17 14:50:52', '2022-03-17 14:50:59', NULL, NULL)",
            ];
        }
        return [
            "INSERT INTO `{$tableName}` VALUES (1, 1, 'InnoDB', 'InnoDB', 'table_engine', 0, 1, NULL, NULL, '2021-06-27 00:37:11', '2021-06-27 13:33:29', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2, 1, 'MyISAM', 'MyISAM', 'table_engine', 0, 1, NULL, NULL, '2021-06-27 00:37:21', '2021-06-27 13:33:29', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (3, 2, '本地存储', '1', 'upload_mode', 99, 1, NULL, NULL, '2021-06-27 13:33:43', '2021-06-27 13:33:43', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (4, 2, '阿里云OSS', '2', 'upload_mode', 98, 1, NULL, NULL, '2021-06-27 13:33:55', '2021-06-27 13:33:55', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (5, 2, '七牛云', '3', 'upload_mode', 97, 1, NULL, NULL, '2021-06-27 13:34:07', '2021-06-27 13:34:07', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (6, 2, '腾讯云COS', '4', 'upload_mode', 96, 1, NULL, NULL, '2021-06-27 13:34:19', '2021-06-27 13:34:19', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (7, 3, '正常', '1', 'data_status', 0, 1, NULL, NULL, '2021-06-27 13:36:51', '2021-06-27 13:37:01', NULL, '0为正常')",
            "INSERT INTO `{$tableName}` VALUES (8, 3, '停用', '2', 'data_status', 0, 1, NULL, NULL, '2021-06-27 13:37:10', '2021-06-27 13:37:10', NULL, '1为停用')",
            "INSERT INTO `{$tableName}` VALUES (9, 4, '统计页面', 'statistics', 'dashboard', 0, 1, NULL, NULL, '2021-08-09 12:53:53', '2021-08-09 12:53:53', NULL, '管理员用')",
            "INSERT INTO `{$tableName}` VALUES (10, 4, '工作台', 'work', 'dashboard', 0, 1, NULL, NULL, '2021-08-09 12:54:18', '2021-08-09 12:54:18', NULL, '员工使用')",
            "INSERT INTO `{$tableName}` VALUES (11, 5, '男', '1', 'sex', 0, 1, NULL, NULL, '2021-08-09 12:55:00', '2021-08-09 12:55:00', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (12, 5, '女', '2', 'sex', 0, 1, NULL, NULL, '2021-08-09 12:55:08', '2021-08-09 12:55:08', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (13, 5, '未知', '3', 'sex', 0, 1, NULL, NULL, '2021-08-09 12:55:16', '2021-08-09 12:55:16', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (14, 6, 'String', '1', 'api_data_type', 7, 1, NULL, NULL, '2021-11-23 10:49:00', '2021-11-23 10:49:00', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (15, 6, 'Integer', '2', 'api_data_type', 6, 1, NULL, NULL, '2021-11-23 10:49:29', '2021-11-23 10:49:29', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (16, 6, 'Array', '3', 'api_data_type', 5, 1, NULL, NULL, '2021-11-23 10:49:38', '2021-11-23 10:49:38', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (17, 6, 'Float', '4', 'api_data_type', 4, 1, NULL, NULL, '2021-11-23 10:49:46', '2021-11-23 10:49:46', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (18, 6, 'Boolean', '5', 'api_data_type', 3, 1, NULL, NULL, '2021-11-23 10:49:54', '2021-11-23 10:49:54', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (19, 6, 'Enum', '6', 'api_data_type', 2, 1, NULL, NULL, '2021-11-23 10:50:17', '2021-11-23 10:50:17', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (20, 6, 'Object', '7', 'api_data_type', 1, 1, NULL, NULL, '2021-11-23 10:50:26', '2021-11-23 10:50:26', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (21, 6, 'File', '8', 'api_data_type', 0, 1, NULL, NULL, '2021-12-25 18:32:48', '2021-12-25 18:32:48', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (22, 7, '通知', '1', 'backend_notice_type', 2, 1, NULL, NULL, '2021-11-11 17:29:27', '2021-11-11 17:30:51', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (23, 7, '公告', '2', 'backend_notice_type', 1, 1, NULL, NULL, '2021-11-11 17:31:42', '2021-11-11 17:31:42', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (24, 8, '所有', 'A', 'request_mode', 5, 1, NULL, NULL, '2021-11-14 17:23:25', '2021-11-14 17:23:25', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (25, 8, 'GET', 'G', 'request_mode', 4, 1, NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (26, 8, 'POST', 'P', 'request_mode', 3, 1, NULL, NULL, '2021-11-14 17:23:38', '2021-11-14 17:23:38', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (27, 8, 'PUT', 'U', 'request_mode', 2, 1, NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (28, 8, 'DELETE', 'D', 'request_mode', 1, 1, NULL, NULL, '2021-11-14 17:23:45', '2021-11-14 17:23:45', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (29, 9, '未生产', '1', 'queue_produce_status', 5, 1, NULL, NULL, '2021-12-25 18:25:28', '2021-12-25 18:25:28', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (30, 9, '生产中', '2', 'queue_produce_status', 4, 1, NULL, NULL, '2021-12-25 18:25:38', '2021-12-25 18:25:38', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (31, 9, '生产成功', '3', 'queue_produce_status', 3, 1, NULL, NULL, '2021-12-25 18:25:50', '2021-12-25 18:25:50', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (32, 9, '生产失败', '4', 'queue_produce_status', 2, 1, NULL, NULL, '2021-12-25 18:26:14', '2021-12-25 18:26:14', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (33, 9, '生产重复', '5', 'queue_produce_status', 1, 1, NULL, NULL, '2021-12-25 18:26:30', '2021-12-25 18:26:30', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (34, 10, '未消费', '1', 'queue_consume_status', 5, 1, NULL, NULL, '2021-12-25 18:26:57', '2021-12-25 18:26:57', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (35, 10, '消费中', '2', 'queue_consume_status', 4, 1, NULL, NULL, '2021-12-25 18:27:07', '2021-12-25 18:27:07', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (36, 10, '消费成功', '3', 'queue_consume_status', 3, 1, NULL, NULL, '2021-12-25 18:27:16', '2021-12-25 18:27:16', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (37, 10, '消费失败', '4', 'queue_consume_status', 2, 1, NULL, NULL, '2021-12-25 18:27:24', '2021-12-25 18:27:24', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (38, 10, '消费重复', '5', 'queue_consume_status', 1, 1, NULL, NULL, '2021-12-25 18:27:35', '2021-12-25 18:27:35', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (39, 11, '通知', 'notice', 'queue_msg_type', 1, 1, NULL, NULL, '2021-12-25 18:30:31', '2021-12-25 18:30:31', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (40, 11, '公告', 'announcement', 'queue_msg_type', 2, 1, NULL, NULL, '2021-12-25 18:31:00', '2021-12-25 18:31:00', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (41, 11, '待办', 'todo', 'queue_msg_type', 3, 1, NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (42, 11, '抄送我的', 'carbon_copy_mine', 'queue_msg_type', 4, 1, NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (43, 11, '私信', 'private_message', 'queue_msg_type', 5, 1, NULL, NULL, '2021-12-25 18:31:26', '2021-12-25 18:31:26', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (44, 12, '图片', 'image', 'attachment_type', 10, 1, NULL, NULL, '2022-03-17 14:49:59', '2022-03-17 14:49:59', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (45, 12, '文档', 'text', 'attachment_type', 9, 1, NULL, NULL, '2022-03-17 14:50:20', '2022-03-17 14:50:49', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (46, 12, '音频', 'audio', 'attachment_type', 8, 1, NULL, NULL, '2022-03-17 14:50:37', '2022-03-17 14:50:52', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (47, 12, '视频', 'video', 'attachment_type', 7, 1, NULL, NULL, '2022-03-17 14:50:45', '2022-03-17 14:50:57', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (48, 12, '应用程序', 'application', 'attachment_type', 6, 1, NULL, NULL, '2022-03-17 14:50:52', '2022-03-17 14:50:59', NULL, NULL)",
        ];
    }
}
