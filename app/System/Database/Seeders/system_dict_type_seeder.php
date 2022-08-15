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

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SystemDictTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Db::table('system_dict_type')->truncate();

        foreach ($this->getData() as $item) {
            Db::insert($item);
        }
    }

    protected function getData(): array
    {
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemDictType::getModel()->getTable();
        return [
            "INSERT INTO `{$tableName}` VALUES (1, '数据表引擎', 'table_engine', 1, NULL, NULL, '2021-06-27 00:36:42', '2021-06-27 13:33:29', NULL, '数据表引擎字典')",
            "INSERT INTO `{$tableName}` VALUES (2, '存储模式', 'upload_mode', 1, NULL, NULL, '2021-06-27 13:33:11', '2021-06-27 13:33:11', NULL, '上传文件存储模式')",
            "INSERT INTO `{$tableName}` VALUES (3, '数据状态', 'data_status', 1, NULL, NULL, '2021-06-27 13:36:16', '2021-06-27 13:36:34', NULL, '通用数据状态')",
            "INSERT INTO `{$tableName}` VALUES (4, '后台首页', 'dashboard', 1, NULL, NULL, '2021-08-09 12:53:17', '2021-08-09 12:53:17', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (5, '性别', 'sex', 1, NULL, NULL, '2021-08-09 12:54:40', '2021-08-09 12:54:40', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (6, '接口数据类型', 'api_data_type', 1, NULL, NULL, '2021-11-22 20:56:03', '2021-11-22 20:56:03', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (7, '后台公告类型', 'backend_notice_type', 1, NULL, NULL, '2021-11-11 17:29:05', '2021-11-11 17:29:14', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (8, '请求方式', 'request_mode', 1, NULL, NULL, '2021-11-14 17:22:52', '2021-11-14 17:22:52', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (9, '队列生产状态', 'queue_produce_status', 1, NULL, NULL, '2021-12-25 18:22:38', '2021-12-25 18:22:38', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (10, '队列消费状态', 'queue_consume_status', 1, NULL, NULL, '2021-12-25 18:23:19', '2021-12-25 18:23:19', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (11, '队列消息类型', 'queue_msg_type', 1, NULL, NULL, '2021-12-25 18:28:40', '2021-12-25 18:28:40', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (12, '附件类型', 'attachment_type', 1, NULL, NULL, '2022-03-17 14:49:23', '2022-03-17 14:49:23', NULL, NULL)",

        ];
    }
}
