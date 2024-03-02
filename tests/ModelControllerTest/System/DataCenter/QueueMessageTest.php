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
use App\System\Model\SystemQueueMessage;

beforeEach(function () {
    $this->prefix = '/system/queueMessage';
});
test('queue message test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'receiveList',
        $this->buildTest('getNoParamsTest') => 'sendList',
        $this->buildTest('getNoParamsTest') => 'getReceiveUser',
    ]);
});

test('update read status', function () {
    $ids = array_column(SystemQueueMessage::query()->select(['id'])->get()->toArray(), 'id');

    testSuccessResponse($this->put($this->prefix . '/updateReadStatus', [
        'ids' => $ids,
    ]));
});
