<?php

namespace Plugin\MineAdmin\AppStore\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Xmo\AppStore\Service\Impl\AppStoreServiceImpl;

#[Controller(prefix: 'plugin/store')]
class IndexController extends MineController
{
    #[Inject]
    public AppStoreServiceImpl $serviceImpl;

    #[GetMapping('index')]
    public function index()
    {
        $data = $this->serviceImpl->list(['page' => 1, 'size' => 10]);
        return $this->success($data);
    }
}