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

namespace Plugin\MineAdmin\CodeGenerator\Controller;

use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;
use Plugin\MineAdmin\CodeGenerator\Service\IndexService;


#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: OperationMiddleware::class, priority: 99)]
class IndexController extends AbstractController
{
    public function __construct(
        private readonly IndexService $service,
    ) {}

    #[Get(
        path: '/admin/plugin/code-generator/tableList',
        operationId: 'tableList',
        summary: '数据表列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['代码生成器']
    )]
    public function tableList(): Result
    {
        return $this->success(
            $this->service->getTableListByCurrentDb()
        );
    }
}
