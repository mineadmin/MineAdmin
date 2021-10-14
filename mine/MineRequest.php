<?php

declare(strict_types=1);
namespace Mine;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Request;
use HyperfExt\Jwt\Exceptions\JwtException;
use Mine\Helper\LoginUser;

class MineRequest extends Request
{
    /**
     * @Inject
     * @var MineResponse
     */
    protected $response;

    /**
     * 获取请求IP
     * @return string
     */
    public function ip(): string
    {
        $ip = $this->getServerParams()['remote_addr'] ?? '0.0.0.0';
        $headers = $this->getHeaders();

        if (isset($headers['x-real-ip'])) {
            $ip = $headers['x-real-ip'][0];
        } else if (isset($headers['x-forwarded-for'])) {
            $ip = $headers['x-forwarded-for'][0];
        } else if (isset($headers['http_x_forwarded_for'])) {
            $ip = $headers['http_x_forwarded_for'][0];
        }

        return $ip;
    }

    /**
     * 获取协议架构
     * @return string
     */
    public function getScheme(): string
    {
        if (isset($this->getHeader('X-scheme')[0])) {
            return $this->getHeader('X-scheme')[0].'://';
        } else {
            return 'http://';
        }
    }
}

