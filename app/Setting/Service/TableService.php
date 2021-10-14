<?php

declare(strict_types=1);
namespace App\Setting\Service;

use Mine\Generator\TableGenerator;

/**
 * 数据表设计服务
 * Class TableService
 * @package App\Setting\Service
 */
class TableService
{
    /**
     * 获取表前缀信息
     * @return string
     */
    public function getTablePrefix(): string
    {
        return env('DB_PREFIX', '');
    }

    /**
     * 创建数据表
     * @param array $data
     * @return bool
     */
    public function createTable(array $data): bool
    {
        return make(TableGenerator::class)->setTableInfo($data)->createTable();
    }
}