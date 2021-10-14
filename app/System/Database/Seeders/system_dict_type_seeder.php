<?php

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
            "INSERT INTO `{$tableName}` VALUES (2035075124896, '数据表引擎', 'table_engine', '0', NULL, NULL, '2021-06-27 00:36:42', '2021-06-27 13:33:29', NULL, '数据表引擎字典')",
            "INSERT INTO `{$tableName}` VALUES (2058928973472, '存储模式', 'upload_mode', '0', NULL, NULL, '2021-06-27 13:33:11', '2021-06-27 13:33:11', NULL, '上传文件存储模式')",
            "INSERT INTO `{$tableName}` VALUES (2059023428768, '数据状态', 'data_status', '0', NULL, NULL, '2021-06-27 13:36:16', '2021-06-27 13:36:34', NULL, '通用数据状态')",
            "INSERT INTO `{$tableName}` VALUES (3959885616288, '后台首页', 'dashboard', '0', NULL, NULL, '2021-08-09 12:53:17', '2021-08-09 12:53:17', NULL, NULL)",
            "INSERT INTO `{$tableName}` VALUES (3959928216736, '性别', 'sex', '0', NULL, NULL, '2021-08-09 12:54:40', '2021-08-09 12:54:40', NULL, NULL)",
        ];
    }
}
