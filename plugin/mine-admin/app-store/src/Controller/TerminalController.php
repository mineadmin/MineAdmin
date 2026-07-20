<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Controller;

use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Result;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Access\Attribute\Permission;
use Plugin\MineAdmin\AppStore\Request\TerminalTaskRequest;
use Plugin\MineAdmin\AppStore\Service\TerminalTaskService;

#[Controller(prefix: 'admin/plugin/store/terminal')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Permission(code: 'plugin:store:terminal')]
class TerminalController extends AbstractController
{
    public function __construct(
        \Hyperf\HttpServer\Contract\RequestInterface $request,
        private readonly TerminalTaskService $service
    ) {
        parent::__construct($request);
    }

    #[PostMapping('tasks')]
    #[Permission(code: 'plugin:store:terminal')]
    public function create(TerminalTaskRequest $request): Result
    {
        return $this->success($this->service->create($request->validated()));
    }

    #[GetMapping('tasks/{taskNo}')]
    #[Permission(code: 'plugin:store:terminal')]
    public function status(string $taskNo): Result
    {
        return $this->success($this->service->status($taskNo));
    }

    #[GetMapping('tasks/{taskNo}/logs')]
    #[Permission(code: 'plugin:store:terminal')]
    public function logs(string $taskNo): Result
    {
        return $this->success($this->service->logs(
            $taskNo,
            (int) $this->request->input('after_seq', 0),
            (int) $this->request->input('limit', 200)
        ));
    }

    #[PostMapping('tasks/{taskNo}/cancel')]
    #[Permission(code: 'plugin:store:terminal:cancel')]
    public function cancel(string $taskNo): Result
    {
        return $this->success($this->service->cancel($taskNo));
    }
}
