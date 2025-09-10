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

namespace Plugin\MineAdmin\AppStore\Controller;

use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Result;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\AppStore\Service\Impl\AppStoreServiceImpl;
use Plugin\MineAdmin\AppStore\Service\Service;

#[Controller(prefix: 'admin/plugin/store')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
class IndexController extends AbstractController
{
    #[Inject]
    public AppStoreServiceImpl $serviceImpl;

    #[Inject]
    public Service $service;

    #[GetMapping('index')]
    public function index(): Result
    {
        return $this->success($this->serviceImpl->list($this->request->all()));
    }

    #[GetMapping('getPayApp')]
    public function getPayApp(): Result
    {
        return $this->success($this->serviceImpl->payApp());
    }

    #[GetMapping('getLocalAppInstallList')]
    public function getLocalAppInstallList(): Result
    {
        return $this->success($this->service->getLocalAppInstallList());
    }

    #[GetMapping('detail')]
    public function detail(): Result
    {
        return $this->success($this->serviceImpl->view($this->request->input('identifier')));
    }

    #[PostMapping('download')]
    public function download(): Result
    {
        return $this->success(['result' => $this->service->download($this->request->all())]);
    }

    #[PostMapping('install')]
    public function install(): Result
    {
        return $this->success(['result' => $this->service->install($this->request->all())]);
    }

    #[PostMapping('unInstall')]
    public function unInstall(): Result
    {
        return $this->success(['result' => $this->service->unInstall($this->request->all())]);
    }

    #[PostMapping('uploadLocalApp')]
    public function uploadLocalApp(): Result
    {
        $this->service->uploadLocalApp($this->request->file('file'));
        return $this->success();
    }

    #[GetMapping('hasAccessToken')]
    public function hasAccessToken(): Result
    {
        return $this->success(['isHas' => env('MINE_ACCESS_TOKEN') !== null]);
    }
}
