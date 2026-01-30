<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Plugin\MineAdmin\CodeGenerator\Service;

use App\Service\IService;
use Hyperf\Database\Schema\Schema;
use Hyperf\Stringable\Str;

final class IndexService extends IService
{
    public function __construct() {}

    public function getTableListByCurrentDb(array $params = []): array
    {
        return $this->getArrayToPageList($params);
    }

    public function getArrayToPageList(?array $params = [], string $pageName = 'page'): array
    {
        $collect = $this->handleArraySearch(collect($this->getArrayData($params)), $params);

        $pageSize = 10;
        $page = 1;

        if ($params[$pageName] ?? false) {
            $page = (int) $params[$pageName];
        }

        if ($params['page_size'] ?? false) {
            $pageSize = (int) $params['page_size'];
        }

        $data = $collect->forPage($page, $pageSize)->toArray();

        return [
            'list' => $this->getCurrentArrayPageBefore($data, $params),
            'total' => $collect->count(),
        ];
    }

    /**
     * 数组数据搜索器.
     */
    protected function handleArraySearch(\Hyperf\Collection\Collection $collect, array $params): \Hyperf\Collection\Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return Str::contains($row['name'], $params['name']);
            });
        }
        return $collect;
    }

    /**
     * 数组当前页数据返回之前处理器，默认对key重置.
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        sort($data);
        return $data;
    }

    /**
     * 设置需要分页的数组数据.
     */
    protected function getArrayData(array $params = []): array
    {
        return Schema::getTables();
    }
}
