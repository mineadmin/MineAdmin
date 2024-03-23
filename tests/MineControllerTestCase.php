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

namespace HyperfTests;

use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

/**
 * @mixin HttpTestCase
 */
trait MineControllerTestCase
{
    protected string $prefix;

    public function actionTest(array $routes)
    {
        foreach ($routes as $route => $rule) {
            $route = $this->substrBuild($route);
            if (method_exists($this, $route)) {
                $this->{$route}(...((array) $rule));
            }
        }
    }

    public function buildTest(string $val)
    {
        return Str::random(3) . $val;
    }

    public function substrBuild(string $val)
    {
        return Str::substr($val, 3);
    }

    public function saveAndUpdate(
        array $successParams,
        array $failParams,
        array $updateSuccessParams,
        array $updateFailParams,
        array $uris = ['save', 'update']
    ): int {
        $saveUri = $this->prefix . '/' . $uris[0];
        foreach ($failParams as $param) {
            expect($this->post($saveUri, $param))->toBeHttpFail();
        }
        $result = $this->post($saveUri, $successParams);
        expect($result)->toBeHttpSuccess();
        $id = Arr::get($result, 'data.id');
        $updateUri = $this->prefix . '/' . $uris[1] . '/' . $id;
        expect($this->put($updateUri, $updateSuccessParams))->toBeHttpSuccess();
        foreach ($updateFailParams as $param) {
            expect($this->put($updateUri, $param))->toBeHttpFail();
        }
        return $id;
    }

    public function remoteTest(string $uri = 'remote')
    {
        expect($this->post($this->prefix . '/' . $uri))->toBeHttpSuccess();
    }

    public function recoveryAndDeleteTest(array $ids, array $uris = ['delete', 'realDelete', 'recovery'])
    {
        foreach ($uris as $url) {
            if ($url === 'recovery') {
                expect($this->put($this->prefix . '/' . $url, compact('ids')))->toBeHttpSuccess();
            } else {
                expect($this->delete($this->prefix . '/' . $url, compact('ids')))->toBeHttpSuccess();
            }
        }
    }

    public function changeStatusTest(int|string $id, int $status = 1, string $uri = 'changeStatus')
    {
        expect($this->put($this->prefix . '/' . $uri, compact('id', 'status')))->toBeHttpSuccess();
    }

    public function getNoParamsTest(string $route, ?\Closure $customer = null): void
    {
        $uri = $this->getUri($route);
        $result = $this->get($uri);
        if ($customer !== null) {
            $customer($result);
        } else {
            expect($result)->toBeHttpSuccess();
        }
    }

    public function numberOperationTest(int|string $id, string $numberName = 'created_by', int $numberValue = 1, string $uri = 'numberOperation')
    {
        expect($this->put($this->prefix . '/' . $uri, compact('id', 'numberName', 'numberValue')))->toBeHttpSuccess();
    }

    public function getParamsMockTest(string $route, null|array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->get($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            expect($result)->toBeHttpSuccess();
        }
    }

    public function postParamsMockTest(string $route, null|array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->post($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            expect($result)->toBeHttpSuccess();
        }
    }

    public function delParamsMockTest(string $route, null|array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->delete($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            expect($result)->toBeHttpSuccess();
        }
    }

    public function putParamsMockTest(string $route, null|array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->put($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            expect($result)->toBeHttpSuccess();
        }
    }

    private function getUri(string $route): string
    {
        return $this->prefix . '/' . $route;
    }

    private function checkPrefix(): void
    {
        if (! property_exists($this, 'prefix')) {
            throw new \RuntimeException('No route prefix found.');
        }
    }
}
