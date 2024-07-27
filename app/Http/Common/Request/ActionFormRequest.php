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

namespace App\Http\Common\Request;

use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Validation\Request\FormRequest;

class ActionFormRequest extends FormRequest
{
    public function attributes(): array
    {
        return array_merge(
            $this->next('common', __FUNCTION__),
            $this->next(
                $this->getAction(),
                __FUNCTION__
            )
        );
    }

    public function rules(): array
    {
        return array_merge(
            $this->next('common', __FUNCTION__),
            $this->next(
                $this->getAction(),
                __FUNCTION__
            )
        );
    }

    public function messages(): array
    {
        return array_merge(
            $this->next('common', __FUNCTION__),
            $this->next(
                $this->getAction(),
                __FUNCTION__
            )
        );
    }

    protected function authorize(): bool
    {
        return true;
    }

    protected function getAction(): ?string
    {
        /**
         * @var Dispatched $dispatch
         */
        $dispatch = $this->getAttribute(Dispatched::class);
        $callback = $dispatch?->handler?->callback;
        if (is_array($callback) && count($callback) === 2) {
            return $callback[1];
        }
        if (is_string($callback)) {
            if (str_contains($callback, '@')) {
                return explode('@', $callback)[1] ?? null;
            }
            if (str_contains($callback, '::')) {
                return explode('::', $callback)[1] ?? null;
            }
        }
        return null;
    }

    protected function next(?string $prefix, string $func): array
    {
        $callName = $prefix . ucfirst($func);
        return method_exists($this, $callName) ? \Hyperf\Support\call([$this, $func]) : [];
    }
}
