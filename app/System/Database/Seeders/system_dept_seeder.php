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

class SystemDeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run()
    {
        Db::table('system_dept')->truncate();
        Db::table('system_dept')->insert(
            [
                'parent_id' => 0,
                'level' => '0',
                'name' => '曼艺科技',
                'leader' => '曼艺',
                'phone' => '16888888888',
                'created_by' => env('SUPER_ADMIN', 1),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
