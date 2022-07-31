<?php

declare(strict_types = 1);
namespace App\System\Mapper;

use App\System\Model\SystemQueueMessage;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;
use Mine\MineModel;

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
        if (isset($params['title'])) {
            $query->where('title', 'like', '%'.$params['title'].'%');
        }

        // 内容类型
        if (isset($params['content_type']) && $params['content_type'] !== 'all') {
            $query->where('content_type', '=', $params['content_type']);
        }

        if (isset($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) === 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0] . '00:00:00', $params['created_at'][1] . '23:59:59' ]
            );
        }

        // 获取收信数据
        if (isset($params['getReceive'])) {
            $query->with(['sendUser' => function($query) {
                $query->select([ 'id', 'username', 'nickname', 'avatar' ]);
            }]);
            $prefix = env('DB_PREFIX');
            $readStatus = $params['read_status'] ?? 'all';
            $sql = <<<sql
                id IN ( 
                    SELECT `message_id` FROM `{$prefix}system_queue_message_receive` WHERE `user_id` = ?
                    AND if (? <> 'all', `read_status` = ?, ' 1 = 1 ')
                )
            sql;
            $query->whereRaw($sql, [ $params['user_id'] ?? user()->getId(), $readStatus, $readStatus ]);
        }

        // 收取发信数据
        if (isset($params['getSend'])) {
            $query->where('send_by', user()->getId());
        }

        return $query;
    }

    /**
     * 获取接收人列表
     * @param int $id
     * @return array
     */
    public function getReceiveUserList(int $id): array
    {
        $prefix = env('DB_PREFIX');
        $paginate = Db::table('system_user as u')
            ->select(Db::raw("{$prefix}u.username, {$prefix}u.nickname, if ({$prefix}r.read_status = 2, '已读', '未读') as read_status "))
            ->join('system_queue_message_receive as r', 'u.id', '=', 'r.user_id')
            ->where('r.message_id', $id)
            ->paginate(
                $params['pageSize'] ?? $this->model::PAGE_SIZE,
                ['*'],
                'page',
                $params['page'] ?? 1
            );

        return $this->setPaginate($paginate);
    }

    /**
     * 保存数据
     * @param array $data
     * @return int
     */
    #[Transaction]
    public function save(array $data): int
    {
        $receiveUsers = $data['receive_users'];
        $this->filterExecuteAttributes($data);
        $model = $this->model::create($data);
        $model->receiveUser()->sync($receiveUsers);
        return $model->{$model->getKeyName()};
    }

    /**
     * 删除消息
     * @param array $ids
     * @return bool
     * @throws \Exception
     */
    #[Transaction]
    public function delete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::find($id);
            if ($model) {
                $model->receiveUser()->detach();
                $model->delete();
            }
        }
        return true;
    }

    /**
     * 更新中间表数据状态
     * @param array $ids
     * @param string $columnName
     * @param string $value
     * @return bool
     */
    public function updateDataStatus(array $ids, string $columnName = 'read_status', int $value = 2): bool
    {
        foreach ($ids as $id) {
            $result = Db::table('system_queue_message_receive')
                ->where('message_id', $id)
                ->where('user_id', user()->getId())
                ->value($columnName);

            if ($result != $value) {
                Db::table('system_queue_message_receive')
                    ->where('message_id', $id)
                    ->where('user_id', user()->getId())
                    ->update([ $columnName => $value ]);
            }
        }

        return true;
    }
}
