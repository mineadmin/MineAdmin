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

namespace Mine\Kernel\Jwt;

use Hyperf\Collection\Arr;
use Hyperf\Contract\ConfigInterface;

final class Factory
{
    public function __construct(
        private readonly ConfigInterface $config,
    ) {}

    public function get(string $name = 'default'): JwtInterface
    {
        return make(Jwt::class, [
            'config' => $this->getConfig($name),
        ]);
    }

    // 获取场景配置
    public function getConfig(string $scene): array
    {
        if ($scene === 'default') {
            return $this->config->get($this->getConfigKey());
        }
        return Arr::merge(
            $this->config->get($this->getConfigKey($scene)),
            $this->config->get($this->getConfigKey())
        );
    }

    private function getConfigKey(string $name = 'default'): string
    {
        return 'jwt.' . $name;
    }
}
