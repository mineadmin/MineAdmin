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
use App\Setting\Model\SettingConfig;
use Hyperf\Database\Seeders\Seeder;

class SettingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        SettingConfig::truncate();
        $data = [
            [
                'group_id' => 1,
                'key' => 'site_copyright',
                'name' => '版权信息',
                'value' => 'Copyright © 2019-2022 MineAdmin. All rights reserved.',
                'input_type' => 'textarea',
                'sort' => 96,
            ],
            [
                'group_id' => 1,
                'key' => 'site_desc',
                'value' => 'MineAdmin',
                'name' => '网站描述',
                'input_type' => 'textarea',
                'sort' => 97,
            ],
            [
                'group_id' => 1,
                'key' => 'site_keywords',
                'value' => '后台管理系统',
                'name' => '网站关键字',
                'input_type' => 'input',
                'sort' => 98,
            ],
            [
                'group_id' => 1,
                'key' => 'site_name',
                'value' => 'MineAdmin',
                'name' => '网站名称',
                'input_type' => 'input',
                'sort' => 99,
            ],
            [
                'group_id' => 1,
                'key' => 'site_record_number',
                'value' => 'xxx',
                'name' => '网站备案号',
                'input_type' => 'input',
                'sort' => 95,
            ],
            [
                'group_id' => 2,
                'key' => 'upload_allow_file',
                'value' => 'txt,doc,docx,xls,xlsx,ppt,pptx,rar,zip,7z,gz,pdf,wps,md',
                'name' => '文件类型',
                'input_type' => 'input',
                'sort' => 0,
            ],
            [
                'group_id' => 2,
                'key' => 'upload_allow_image',
                'value' => 'jpg,jpeg,png,gif,svg,bmp',
                'name' => '图片类型',
                'input_type' => 'input',
                'sort' => 0,
            ],
            [
                'group_id' => 2,
                'key' => 'upload_mode',
                'value' => '1',
                'name' => '上传模式',
                'input_type' => 'select',
                'config_select_data' => '[{"label":"本地上传","value":"1"},{"label":"阿里云OSS","value":"2"},{"label":"七牛云","value":"3"},{"label":"腾讯云COS","value":"4"}]',
                'sort' => 99,
            ],
        ];
        foreach ($data as $value) {
            SettingConfig::create($value);
        }
    }
}
