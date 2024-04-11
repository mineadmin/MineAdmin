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

namespace App\System\Mapper;

use App\System\Model\SystemQueueMessage;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 信息管理Mapper类.
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
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['title']) && filled($params['title'])) {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }

        // 内容类型
        if (isset($params['content_type']) && filled($params['content_type']) && $params['content_type'] !== 'all') {
            $query->where('content_type', '=', $params['content_type']);
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) === 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }

        // 获取收信数据
        if (isset($params['getReceive']) && filled($params['getReceive'])) {
            $query->with(['sendUser' => function ($query) {
                $query->select(['id', 'username', 'nickname', 'avatar']);
            }]);
            $prefix = env('DB_PREFIX');
            $readStatus = $params['read_status'] ?? 'all';

            if (env('DB_DRIVER') == 'pgsql') {
                $sql = <<<sql
                    id IN ( 
                        SELECT "message_id" FROM "{$prefix}system_queue_message_receive" WHERE "user_id" = ?
                        AND (CASE WHEN CAST(? AS varchar) <> 'all' THEN CAST("read_status" as varchar) = ? ELSE  1 = 1  END)
                    )
                sql;
            } else {
                $sql = <<<sql
                    id IN (
                        SELECT `message_id` FROM `{$prefix}system_queue_message_receive` WHERE `user_id` = ?
                        AND if (? <> 'all', `read_status` = ?, ' 1 = 1 ')
                    )
                sql;
            }
            $query->whereRaw($sql, [$params['user_id'] ?? user()->getId(), $readStatus, $readStatus]);
        }

        // 收取发信数据
        if (isset($params['getSend']) && filled($params['getSend'])) {
            $query->where('send_by', user()->getId());
        }

        return $query;
    }

    /**
     * 获取接收人列表.
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
     * 保存数据.
     */
    #[Transaction]
    public function save(array $data): mixed
    {
        $receiveUsers = $data['receive_users'];
        $this->filterExecuteAttributes($data);
        $model = $this->model::create($data);
        $model->receiveUser()->sync($receiveUsers);
        return $model->{$model->getKeyName()};
    }

    /**
     * 删除消息.
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
                    ->update([$columnName => $value]);
            }
        }

        return true;
    }
}
