<?php

declare(strict_types = 1);
namespace App\System\Mapper;

use App\System\Model\SystemNotice;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 通知管理Mapper类
 */
class SystemNoticeMapper extends AbstractMapper
{
    /**
     * @var SystemNotice
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemNotice::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 公告标题
        if (isset($params['title'])) {
            $query->where('title', '=', $params['title']);
        }

        // 公告类型（1通知 2公告）
        if (isset($params['type'])) {
            $query->where('type', '=', $params['type']);
        }

        // 公告内容
        if (isset($params['content'])) {
            $query->where('content', '=', $params['content']);
        }

        // 状态 (0正常 1停用)
        if (isset($params['status'])) {
            $query->where('status', '=', $params['status']);
        }

        // 排序
        if (isset($params['sort'])) {
            $query->where('sort', '=', $params['sort']);
        }

        return $query;
    }
}