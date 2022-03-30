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
            return Schema::getColumnTypeListing(str_replace(env('DB_PREFIX'), '', $table));
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