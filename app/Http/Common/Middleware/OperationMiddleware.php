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

namespace App\Http\Common\Middleware;

use App\Http\Common\Event\RequestOperationEvent;
use App\Http\CurrentUser;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\MultipleAnnotation;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\Patch;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Mine\Support\Request;
use Mine\Support\Traits\ParserRouterTrait;
use OpenApi\Annotations\Operation;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OperationMiddleware implements MiddlewareInterface
{
    use ParserRouterTrait;

    public const PATH_ATTRIBUTES = [
        Post::class,
        Get::class,
        Delete::class,
        Patch::class,
        Put::class,
    ];

    public function __construct(
        private readonly CurrentUser $user,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ContainerInterface $container
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        $parseResult = $this->parse($dispatched?->handler?->callback);
        if (! $parseResult) {
            return $handler->handle($request);
        }
        [$controller,$method] = $parseResult;
        $operator = $this->getClassMethodPathAttribute($controller, $method);
        if ($operator !== null) {
            $this->dispatcher->dispatch(new RequestOperationEvent(
                $this->user->id(),
                $operator->summary,
                $request->getUri()->getPath(),
                $this->getRealIp($request),
                $request->getMethod(),
            ));
        }
        return $handler->handle($request);
    }

    private function getClassMethodPathAttribute(string $controller, string $method): ?Operation
    {
        foreach (static::PATH_ATTRIBUTES as $attribute) {
            $annotations = AnnotationCollector::getClassMethodAnnotation($controller, $method);
            if (empty($annotations[$attribute]) || ! ($annotations[$attribute] instanceof MultipleAnnotation)) {
                continue;
            }
            $multiple = $annotations[$attribute];
            if ($annotation = Arr::first($multiple->toAnnotations())) {
                return $annotation;
            }
        }
        return null;
    }

    /**
     * 获取客户端真实IP.
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getRealIp(ServerRequestInterface $request): string
    {
        $headers = $request->getHeaders();
        return ($headers['x-real-ip'][0] ?? null)
            ?? ($headers['x-forwarded-for'][0] ?? null)
            ?? ($headers['http_x_forwarded_for'][0] ?? null)
            ?? ($request->getServerParams()['remote_addr'] ?? null)
            ?? '0.0.0.0';
    }
}
