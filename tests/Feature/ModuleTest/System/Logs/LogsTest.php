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
use App\Model\System\LoginLog;
use App\Model\System\OperLog;
use App\Model\System\QueueLog;
use Carbon\Carbon;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/logs';
});

test('logs test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'getLoginLogPageList',
        $this->buildTest('getNoParamsTest') => 'getOperLogPageList',
        $this->buildTest('getNoParamsTest') => 'getApiLogPageList',
        $this->buildTest('getNoParamsTest') => 'getQueueLogPageList',
    ]);

    OperLog::create([
        'username' => 'aaa',
        'method' => 'xxx',
        'router' => 'xxx',
        'service_name' => 'xxx',
        'ip' => '127.0.0.1',
        'ip_location' => 'xxx',
        'request_data' => 'xxx',
        'response_code' => 'xxx',
        'response_data' => 'xxx',
    ]);
    expect($this->delete($this->prefix . '/deleteOperLog', [
        'ids' => array_column(OperLog::query()->select(['id'])->get()->toArray(), 'id'),
    ]))->toBeHttpSuccess();
    QueueLog::create([
        'exchange_name' => 'xxx',
        'routing_key_name' => 'xxx',
        'queue_name' => 'xxx',
        'queue_content' => 'xxx',
        'log_content' => 'xxxx',
        'produce_status' => 1,
        'consume_status' => 2,
        'delay_time' => 3,
    ]);

    QueueLog::create([
        'exchange_name' => 'xxx',
        'routing_key_name' => 'xxx',
        'queue_name' => 'xxx',
        'queue_content' => 'xxx',
        'log_content' => 'xxxx',
        'produce_status' => 1,
        'consume_status' => 2,
        'delay_time' => 3,
    ]);
    expect($this->delete($this->prefix . '/deleteQueueLog', [
        'ids' => array_column(QueueLog::query()->limit(1)->select(['id'])->get()->toArray(), 'id'),
    ]))->toBeHttpSuccess();
    LoginLog::create([
        'username' => Str::random(10),
        'ip' => '127.0.0.1',
        'ip_location' => 'xxx',
        'login_time' => Carbon::now(),
        'os' => 'xxx',
        'browser' => 'xxx',
    ]);
    expect($this->delete($this->prefix . '/deleteLoginLog', [
        'ids' => array_column(LoginLog::query()->select(['id'])->get()->toArray(), 'id'),
    ]))->toBeHttpSuccess();
});
