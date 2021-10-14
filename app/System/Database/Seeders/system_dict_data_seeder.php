<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SystemDictDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
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
        $tableName = env('DB_PREFIX') . \App\System\Model\SystemDictData::getModel()->getTable();
        return [
            "INSERT INTO `{$tableName}` VALUES (2035090111136, 2035075124896, 'InnoDB', 'InnoDB', 'table_engine', 0, '0', NULL, NULL, '2021-06-27 00:37:11', '2021-06-27 13:33:29', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2035095441568, 2035075124896, 'MyISAM', 'MyISAM', 'table_engine', 0, '0', NULL, NULL, '2021-06-27 00:37:21', '2021-06-27 13:33:29', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2058945281696, 2058928973472, '本地存储', 'local', 'upload_mode', 99, '0', NULL, NULL, '2021-06-27 13:33:43', '2021-06-27 13:33:43', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2058951566496, 2058928973472, '七牛云', 'qiniu', 'upload_mode', 98, '0', NULL, NULL, '2021-06-27 13:33:55', '2021-06-27 13:33:55', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2058957471392, 2058928973472, '阿里云OSS', 'oss', 'upload_mode', 97, '0', NULL, NULL, '2021-06-27 13:34:07', '2021-06-27 13:34:07', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2058963571360, 2058928973472, '腾讯云COS', 'cos', 'upload_mode', 96, '0', NULL, NULL, '2021-06-27 13:34:19', '2021-06-27 13:34:19', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (2059041780384, 2059023428768, '正常', '0', 'data_status', 0, '0', NULL, NULL, '2021-06-27 13:36:51', '2021-06-27 13:37:01', NULL, '0为正常')",
            "INSERT INTO `{$tableName}` VALUES (2059051223200, 2059023428768, '停用', '1', 'data_status', 0, '0', NULL, NULL, '2021-06-27 13:37:10', '2021-06-27 13:37:10', NULL, '1为停用')",
            "INSERT INTO `{$tableName}` VALUES (3959904132768, 3959885616288, '统计页面', 'statistics', 'dashboard', 0, '0', NULL, NULL, '2021-08-09 12:53:53', '2021-08-09 12:53:53', NULL, '管理员用')",
            "INSERT INTO `{$tableName}` VALUES (3959916887200, 3959885616288, '工作台', 'work', 'dashboard', 0, '0', NULL, NULL, '2021-08-09 12:54:18', '2021-08-09 12:54:18', NULL, '员工使用')",
            "INSERT INTO `{$tableName}` VALUES (3959938423968, 3959928216736, '男', '0', 'sex', 0, '0', NULL, NULL, '2021-08-09 12:55:00', '2021-08-09 12:55:00', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (3959942491808, 3959928216736, '女', '1', 'sex', 0, '0', NULL, NULL, '2021-08-09 12:55:08', '2021-08-09 12:55:08', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (3959946307744, 3959928216736, '未知', '2', 'sex', 0, '0', NULL, NULL, '2021-08-09 12:55:16', '2021-08-09 12:55:16', NULL, NULL)",
        ];
    }
}
