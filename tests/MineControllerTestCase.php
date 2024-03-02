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
            testFailResponse($this->post($saveUri, $param));
        }
        $result = $this->post($saveUri, $successParams);
        testSuccessResponse($result);
        $id = Arr::get($result, 'data.id');
        $updateUri = $this->prefix . '/' . $uris[1] . '/' . $id;
        testSuccessResponse($this->put($updateUri, $updateSuccessParams));
        foreach ($updateFailParams as $param) {
            testFailResponse($this->put($updateUri, $param));
        }
        return $id;
    }

    public function remoteTest(string $uri = 'remote')
    {
        testSuccessResponse($this->post($this->prefix . '/' . $uri));
    }

    public function recoveryAndDeleteTest(array $ids, array $uris = ['delete', 'realDelete', 'recovery'])
    {
        foreach ($uris as $url) {
            testSuccessResponse($this->delete($this->prefix . '/' . $url, compact('ids')));
        }
    }

    public function changeStatusTest(int|string $id, int $status = 1, string $uri = 'changeStatus')
    {
        testSuccessResponse($this->put($this->prefix . '/' . $uri, compact('id', 'status')));
    }

    public function getNoParamsTest(string $route, ?\Closure $customer = null): void
    {
        $uri = $this->getUri($route);
        $result = $this->get($uri);
        if ($customer !== null) {
            $customer($result);
        } else {
            testSuccessResponse($result);
        }
    }

    public function numberOperationTest(int|string $id, string $numberName = 'created_by', int $numberValue = 1, string $uri = 'numberOperation')
    {
        testSuccessResponse($this->put($this->prefix . '/' . $uri, compact('id', 'numberName', 'numberValue')));
    }

    public function getParamsMockTest(string $route, array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->get($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            testSuccessResponse($result);
        }
    }

    public function postParamsMockTest(string $route, array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->post($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            testSuccessResponse($result);
        }
    }

    public function delParamsMockTest(string $route, array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->delete($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            testSuccessResponse($result);
        }
    }

    public function putParamsMockTest(string $route, array|\Closure $customer = null, ?\Closure $customerTest = null): void
    {
        $uri = $this->getUri($route);
        $params = $customer instanceof \Closure ? $customer() : $customer;
        $result = $this->put($uri, $params);
        if ($customerTest !== null) {
            $customerTest($result);
        } else {
            testSuccessResponse($result);
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
