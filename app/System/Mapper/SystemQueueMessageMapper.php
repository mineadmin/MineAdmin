<?php

declare(strict_types = 1);
namespace App\System\Mapper;

use App\System\Model\SystemQueueMessage;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 信息管理Mapper类
 */
class SystemQueueMessageMapper extends AbstractMapper
{
    /**
     * @var SystemQueueMessage
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemQueueMessage::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 内容ID
        if (isset($params['content_id'])) {
            $query->where('content_id', '=', $params['content_id']);
        }

        // 内容类型
        if (isset($params['content_type'])) {
            $query->where('content_type', '=', $params['content_type']);
        }

        // 发送内容
        if (isset($params['content'])) {
            $query->where('content', '=', $params['content']);
        }

        // 接收人ID
        if (isset($params['receive_by'])) {
            $query->where('receive_by', '=', $params['receive_by']);
        }

        // 发送状态 0:待发送 1:发送中 2:发送成功 3:发送失败
        if (isset($params['send_status'])) {
            $query->where('send_status', '=', $params['send_status']);
        }

        return $query;
    }
}
