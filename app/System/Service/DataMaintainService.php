<?php

declare(strict_types=1);
namespace App\System\Service;

use Hyperf\Database\Model\Collection;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractService;

class DataMaintainService extends AbstractService
{
    /**
     * 获取表状态分页列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getPageList(?array $params = [], bool $isScope = true): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 数组数据搜索器
     * @param \Hyperf\Utils\Collection $collect
     * @param array $params
     * @return Collection
     */
    protected function handleArraySearch(\Hyperf\Utils\Collection $collect, array $params): \Hyperf\Utils\Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row->Name, $params['name']);
            });
        }
        if ($params['engine'] ?? false) {
            $collect = $collect->where('Engine', $params['engine']);
        }
        return $collect;
    }

    /**
     * 数组当前页数据返回之前处理器，默认对key重置
     * @param array $data
     * @param array $params
     * @return array
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        $tables = [];
        foreach ($data as $item) {
            $tables[] = array_change_key_case((array)$item);
        }
        return $tables;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        return Db::select(Db::raw("SHOW TABLE STATUS WHERE name NOT LIKE '%migrations'")->getValue());
    }

    /**
     * 获取表字段
     * @param string $table
     * @return array
     */
    public function getColumnList(string $table): array
    {
        if ($table) {
            //从数据库中获取表字段信息
            $sql = "SELECT * FROM `information_schema`.`columns` "
                . "WHERE TABLE_SCHEMA = ? AND table_name = ? "
                . "ORDER BY ORDINAL_POSITION";
            //加载主表的列
            $columnList = [];
            foreach (Db::select($sql, [env('DB_DATABASE'), str_replace(env('DB_PREFIX'), '', $table)]) as $column) {
                $columnList[] = [
                    'column_key' => $column->COLUMN_KEY,
                    'column_name'=> $column->COLUMN_NAME,
                    'data_type' => $column->DATA_TYPE,
                    'column_comment' => $column->COLUMN_COMMENT,
                    'extra' => $column->EXTRA,
                    'column_type' => $column->COLUMN_TYPE,
                    'is_nullable' => $column->IS_NULLABLE,
                ];
            }
            return $columnList;
        } else {
            return [];
        }
    }

    /**
     * 优化表
     * @param array $tables
     * @return bool
     */
    public function optimize(array $tables): bool
    {
        foreach ($tables as $table) {
            Db::select('optimize table `?`', [$table]);
        }
        return true;
    }

    /**
     * 清理表碎片
     * @param array $tables
     * @return bool
     */
    public function fragment(array $tables): bool
    {
        foreach ($tables as $table) {
            Db::select('analyze table `?`', [$table]);
        }
        return true;
    }


}