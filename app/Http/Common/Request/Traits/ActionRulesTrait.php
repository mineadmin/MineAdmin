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

namespace App\Http\Common\Request\Traits;

use Hyperf\HttpServer\Router\Dispatched;

/**
 * @phpstan-ignore-next-line
 */
trait ActionRulesTrait
{
    /**
     * 获取验证规则.
     */
    public function rules(): array
    {
        return array_merge(
            $this->callNextFunction('common', __FUNCTION__),
            $this->callNextFunction($this->getAction(), __FUNCTION__)
        );
    }

    /**
     * 获取自定义消息.
     */
    public function messages(): array
    {
        return array_merge(
            $this->callNextFunction('common', __FUNCTION__),
            $this->callNextFunction($this->getAction(), __FUNCTION__)
        );
    }

    /**
     * 获取自定义属性.
     */
    public function attributes(): array
    {
        return array_merge(
            parent::attributes(),
            $this->callNextFunction('common', __FUNCTION__),
            $this->callNextFunction($this->getAction(), __FUNCTION__)
        );
    }

    /**
     * 动态调用指定前缀的方法.
     */
    protected function callNextFunction(?string $prefix, string $function): array
    {
        if ($prefix === null) {
            return [];
        }
        $callName = $prefix . ucfirst($function);
        return method_exists($this, $callName) ? \Hyperf\Support\call([$this, $callName]) : [];
    }

    /**
     * 获取当前控制器方法名.
     */
    protected function getAction(): ?string
    {
        /**
         * @var Dispatched $dispatch
         */
        $dispatch = $this->getAttribute(Dispatched::class);
        $callback = $dispatch?->handler?->callback;
        if (\is_array($callback) && \count($callback) === 2) {
            return $callback[1];
        }
        if (\is_string($callback)) {
            if (str_contains($callback, '@')) {
                return explode('@', $callback)[1] ?? null;
            }
            if (str_contains($callback, '::')) {
                return explode('::', $callback)[1] ?? null;
            }
        }
        return null;
    }
}
