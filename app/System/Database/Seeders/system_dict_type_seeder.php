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
use App\System\Model\SystemDictType;
use Hyperf\Database\Seeders\Seeder;

class SystemDictTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        SystemDictType::truncate();
        $data = $this->data();

        foreach ($data as $value) {
            SystemDictType::create($value);
        }
    }

    public function data(): array
    {
        return [
            ['name' => '数据表引擎', 'code' => 'table_engine', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 00:36:42', 'updated_at' => '2021-06-27 13:33:29', 'deleted_at' => null, 'remark' => '数据表引擎字典'],
            ['name' => '存储模式', 'code' => 'upload_mode', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:33:11', 'updated_at' => '2021-06-27 13:33:11', 'deleted_at' => null, 'remark' => '上传文件存储模式'],
            ['name' => '数据状态', 'code' => 'data_status', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-06-27 13:36:16', 'updated_at' => '2021-06-27 13:36:34', 'deleted_at' => null, 'remark' => '通用数据状态'],
            ['name' => '后台首页', 'code' => 'dashboard', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:53:17', 'updated_at' => '2021-08-09 12:53:17', 'deleted_at' => null, 'remark' => ''],
            ['name' => '性别', 'code' => 'sex', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-08-09 12:54:40', 'updated_at' => '2021-08-09 12:54:40', 'deleted_at' => null, 'remark' => ''],
            ['name' => '接口数据类型', 'code' => 'api_data_type', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-22 20:56:03', 'updated_at' => '2021-11-22 20:56:03', 'deleted_at' => null, 'remark' => ''],
            ['name' => '后台公告类型', 'code' => 'backend_notice_type', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-11 17:29:05', 'updated_at' => '2021-11-11 17:29:14', 'deleted_at' => null, 'remark' => ''],
            ['name' => '请求方式', 'code' => 'request_mode', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-11-14 17:22:52', 'updated_at' => '2021-11-14 17:22:52', 'deleted_at' => null, 'remark' => ''],
            ['name' => '队列生产状态', 'code' => 'queue_produce_status', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:22:38', 'updated_at' => '2021-12-25 18:22:38', 'deleted_at' => null, 'remark' => ''],
            ['name' => '队列消费状态', 'code' => 'queue_consume_status', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:23:19', 'updated_at' => '2021-12-25 18:23:19', 'deleted_at' => null, 'remark' => ''],
            ['name' => '队列消息类型', 'code' => 'queue_msg_type', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2021-12-25 18:28:40', 'updated_at' => '2021-12-25 18:28:40', 'deleted_at' => null, 'remark' => ''],
            ['name' => '附件类型', 'code' => 'attachment_type', 'status' => 1, 'created_by' => 0, 'updated_by' => 0, 'created_at' => '2022-03-17 14:49:23', 'updated_at' => '2022-03-17 14:49:23', 'deleted_at' => null, 'remark' => ''],
        ];
    }
}
