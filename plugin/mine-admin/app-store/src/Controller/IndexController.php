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

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\MineController;
use Plugin\MineAdmin\AppStore\Service\Service;
use Psr\Http\Message\ResponseInterface;
use Mine\AppStore\Service\Impl\AppStoreServiceImpl;

#[Controller(prefix: 'plugin/store')]
class IndexController extends MineController
{
    #[Inject]
    public AppStoreServiceImpl $serviceImpl;

    #[Inject]
    public Service $service;

    #[GetMapping('index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->serviceImpl->list($this->request->all()));
    }

    #[GetMapping('getPayApp')]
    public function getPayApp(): ResponseInterface
    {
        return $this->success($this->serviceImpl->payApp());
    }

    #[GetMapping('getLocalAppInstallList')]
    public function getLocalAppInstallList(): ResponseInterface
    {
        return $this->success($this->service->getLocalAppInstallList());
    }

    #[GetMapping('getLocalAppList')]
    public function getLocalAppList(): ResponseInterface
    {
        return $this->success($this->service->getLocalAppList());
    }

    #[GetMapping('detail')]
    public function detail(): ResponseInterface
    {
        return $this->success($this->serviceImpl->view($this->request->input('identifier')));
    }

    #[PostMapping('download')]
    public function download(): ResponseInterface
    {
        return $this->success( ['result' => $this->service->download($this->request->all())] );
    }

    #[PostMapping('install')]
    public function install(): ResponseInterface
    {
        return $this->success( ['result' => $this->service->install($this->request->all())] );
    }

    #[PostMapping('unInstall')]
    public function unInstall(): ResponseInterface
    {
        return $this->success( ['result' => $this->service->unInstall($this->request->all())] );
    }

    #[GetMapping('hasAccessToken')]
    public function hasAccessToken(): ResponseInterface
    {
        return $this->success(['isHas' => ! is_null(env('MINE_ACCESS_TOKEN'))]);
    }
}
