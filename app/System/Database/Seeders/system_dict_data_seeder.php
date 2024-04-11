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

class SystemDictDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        SystemDictData::truncate();
        $data = $this->data();
        foreach ($data as $value) {
            SystemDictData::create($value);
        }
    }

    public function data(): array
    {
        return [
            ['type_id' => 1, 'label' => 'InnoDB', 'value' => 'InnoDB', 'code' => 'table_engine', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 00:37:11', 'updated_at' => '2021-06-27 13:33:29', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 1, 'label' => 'MyISAM', 'value' => 'MyISAM', 'code' => 'table_engine', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 00:37:21', 'updated_at' => '2021-06-27 13:33:29', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 2, 'label' => '本地存储', 'value' => '1', 'code' => 'upload_mode', 'sort' => 99, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:33:43', 'updated_at' => '2021-06-27 13:33:43', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 2, 'label' => '阿里云OSS', 'value' => '2', 'code' => 'upload_mode', 'sort' => 98, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:33:55', 'updated_at' => '2021-06-27 13:33:55', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 2, 'label' => '七牛云', 'value' => '3', 'code' => 'upload_mode', 'sort' => 97, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:34:07', 'updated_at' => '2021-06-27 13:34:07', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 2, 'label' => '腾讯云COS', 'value' => '4', 'code' => 'upload_mode', 'sort' => 96, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:34:19', 'updated_at' => '2021-06-27 13:34:19', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 3, 'label' => '正常', 'value' => '1', 'code' => 'data_status', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:36:51', 'updated_at' => '2021-06-27 13:37:01', 'deleted_at' => null, 'remark' => '0为正常'],
            ['type_id' => 3, 'label' => '停用', 'value' => '2', 'code' => 'data_status', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:37:10', 'updated_at' => '2021-06-27 13:37:10', 'deleted_at' => null, 'remark' => '1为停用'],
            ['type_id' => 4, 'label' => '统计页面', 'value' => 'statistics', 'code' => 'dashboard', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:53:53', 'updated_at' => '2021-08-09 12:53:53', 'deleted_at' => null, 'remark' => '管理员用'],
            ['type_id' => 4, 'label' => '工作台', 'value' => 'work', 'code' => 'dashboard', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:54:18', 'updated_at' => '2021-08-09 12:54:18', 'deleted_at' => null, 'remark' => '员工使用'],
            ['type_id' => 5, 'label' => '男', 'value' => '1', 'code' => 'sex', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:55:00', 'updated_at' => '2021-08-09 12:55:00', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 5, 'label' => '女', 'value' => '2', 'code' => 'sex', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:55:08', 'updated_at' => '2021-08-09 12:55:08', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 5, 'label' => '未知', 'value' => '3', 'code' => 'sex', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:55:16', 'updated_at' => '2021-08-09 12:55:16', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'String', 'value' => '1', 'code' => 'api_data_type', 'sort' => 7, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:49:00', 'updated_at' => '2021-11-23 10:49:00', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'Integer', 'value' => '2', 'code' => 'api_data_type', 'sort' => 6, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:49:29', 'updated_at' => '2021-11-23 10:49:29', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'Array', 'value' => '3', 'code' => 'api_data_type', 'sort' => 5, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:49:38', 'updated_at' => '2021-11-23 10:49:38', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'Float', 'value' => '4', 'code' => 'api_data_type', 'sort' => 4, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:49:46', 'updated_at' => '2021-11-23 10:49:46', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'Boolean', 'value' => '5', 'code' => 'api_data_type', 'sort' => 3, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:49:54', 'updated_at' => '2021-11-23 10:49:54', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'Enum', 'value' => '6', 'code' => 'api_data_type', 'sort' => 2, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:50:17', 'updated_at' => '2021-11-23 10:50:17', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'Object', 'value' => '7', 'code' => 'api_data_type', 'sort' => 1, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-23 10:50:26', 'updated_at' => '2021-11-23 10:50:26', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 6, 'label' => 'File', 'value' => '8', 'code' => 'api_data_type', 'sort' => 0, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:32:48', 'updated_at' => '2021-12-25 18:32:48', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 7, 'label' => '通知', 'value' => '1', 'code' => 'backend_notice_type', 'sort' => 2, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-11 17:29:27', 'updated_at' => '2021-11-11 17:30:51', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 7, 'label' => '公告', 'value' => '2', 'code' => 'backend_notice_type', 'sort' => 1, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-11 17:31:42', 'updated_at' => '2021-11-11 17:31:42', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 8, 'label' => '所有', 'value' => 'A', 'code' => 'request_mode', 'sort' => 5, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-14 17:23:25', 'updated_at' => '2021-11-14 17:23:25', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 8, 'label' => 'GET', 'value' => 'G', 'code' => 'request_mode', 'sort' => 4, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-14 17:23:45', 'updated_at' => '2021-11-14 17:23:45', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 8, 'label' => 'POST', 'value' => 'P', 'code' => 'request_mode', 'sort' => 3, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-14 17:23:38', 'updated_at' => '2021-11-14 17:23:38', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 8, 'label' => 'PUT', 'value' => 'U', 'code' => 'request_mode', 'sort' => 2, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-14 17:23:45', 'updated_at' => '2021-11-14 17:23:45', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 8, 'label' => 'DELETE', 'value' => 'D', 'code' => 'request_mode', 'sort' => 1, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-14 17:23:45', 'updated_at' => '2021-11-14 17:23:45', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 9, 'label' => '未生产', 'value' => '1', 'code' => 'queue_produce_status', 'sort' => 5, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:25:28', 'updated_at' => '2021-12-25 18:25:28', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 9, 'label' => '生产中', 'value' => '2', 'code' => 'queue_produce_status', 'sort' => 4, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:25:38', 'updated_at' => '2021-12-25 18:25:38', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 9, 'label' => '生产成功', 'value' => '3', 'code' => 'queue_produce_status', 'sort' => 3, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:25:50', 'updated_at' => '2021-12-25 18:25:50', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 9, 'label' => '生产失败', 'value' => '4', 'code' => 'queue_produce_status', 'sort' => 2, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:26:14', 'updated_at' => '2021-12-25 18:26:14', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 9, 'label' => '生产重复', 'value' => '5', 'code' => 'queue_produce_status', 'sort' => 1, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:26:30', 'updated_at' => '2021-12-25 18:26:30', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 10, 'label' => '未消费', 'value' => '1', 'code' => 'queue_consume_status', 'sort' => 5, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:26:57', 'updated_at' => '2021-12-25 18:26:57', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 10, 'label' => '消费中', 'value' => '2', 'code' => 'queue_consume_status', 'sort' => 4, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:27:07', 'updated_at' => '2021-12-25 18:27:07', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 10, 'label' => '消费成功', 'value' => '3', 'code' => 'queue_consume_status', 'sort' => 3, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:27:16', 'updated_at' => '2021-12-25 18:27:16', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 10, 'label' => '消费失败', 'value' => '4', 'code' => 'queue_consume_status', 'sort' => 2, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:27:24', 'updated_at' => '2021-12-25 18:27:24', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 10, 'label' => '消费重复', 'value' => '5', 'code' => 'queue_consume_status', 'sort' => 1, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:27:35', 'updated_at' => '2021-12-25 18:27:35', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 11, 'label' => '通知', 'value' => 'notice', 'code' => 'queue_msg_type', 'sort' => 1, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:30:31', 'updated_at' => '2021-12-25 18:30:31', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 11, 'label' => '公告', 'value' => 'announcement', 'code' => 'queue_msg_type', 'sort' => 2, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:31:00', 'updated_at' => '2021-12-25 18:31:00', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 11, 'label' => '待办', 'value' => 'todo', 'code' => 'queue_msg_type', 'sort' => 3, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:31:26', 'updated_at' => '2021-12-25 18:31:26', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 11, 'label' => '抄送我的', 'value' => 'carbon_copy_mine', 'code' => 'queue_msg_type', 'sort' => 4, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:31:26', 'updated_at' => '2021-12-25 18:31:26', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 11, 'label' => '私信', 'value' => 'private_message', 'code' => 'queue_msg_type', 'sort' => 5, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:31:26', 'updated_at' => '2021-12-25 18:31:26', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 12, 'label' => '图片', 'value' => 'image', 'code' => 'attachment_type', 'sort' => 10, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2022-03-17 14:49:59', 'updated_at' => '2022-03-17 14:49:59', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 12, 'label' => '文档', 'value' => 'text', 'code' => 'attachment_type', 'sort' => 9, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2022-03-17 14:50:20', 'updated_at' => '2022-03-17 14:50:49', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 12, 'label' => '音频', 'value' => 'audio', 'code' => 'attachment_type', 'sort' => 8, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2022-03-17 14:50:37', 'updated_at' => '2022-03-17 14:50:52', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 12, 'label' => '视频', 'value' => 'video', 'code' => 'attachment_type', 'sort' => 7, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2022-03-17 14:50:45', 'updated_at' => '2022-03-17 14:50:57', 'deleted_at' => null, 'remark' => ''],
            ['type_id' => 12, 'label' => '应用程序', 'value' => 'application', 'code' => 'attachment_type', 'sort' => 6, 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2022-03-17 14:50:52', 'updated_at' => '2022-03-17 14:50:59', 'deleted_at' => null, 'remark' => ''],
        ];
    }
}
