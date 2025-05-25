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

namespace App\Http\Common\Event;

use Hyperf\Context\ApplicationContext;
use Mine\Support\Request;

class RequestOperationEvent
{
    public function __construct(
        private readonly int $userId,
        private readonly string $operation,
        private readonly string $path,
        private readonly string $ip,
        private readonly string $method = 'GET',
        private readonly string $remark = ''
    ) {}

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getIp(): string
    {
        $request = ApplicationContext::getContainer()->get(Request::class);
        $headers = $request->getHeaders();
        return $headers['x-real-ip'][0]
            ?? $headers['x-forwarded-for'][0]
            ?? $headers['http_x_forwarded_for'][0]
            ?? $request->getServerParams()['remote_addr']
            ?? $this->ip;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRemark(): string
    {
        return $this->remark;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
