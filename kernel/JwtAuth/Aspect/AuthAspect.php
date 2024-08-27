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

namespace Mine\Kernel\JwtAuth\Aspect;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Hyperf\Context\RequestContext;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Kernel\Jwt\Factory;
use Mine\Kernel\JwtAuth\Annotation\Auth;

final class AuthAspect extends AbstractAspect
{
    public array $annotations = [
        Auth::class,
    ];

    public function __construct(private readonly Factory $jwtFactory) {}

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $meta = $proceedingJoinPoint->getAnnotationMetadata();
        if (! empty($meta->method[Auth::class])) {
            $annotation = $meta->method[Auth::class];
            $name = $annotation->name;
        }

        if (! empty($meta->class[Auth::class])) {
            $annotation = $meta->class[Auth::class];
            $name = $annotation->name;
        }
        if (! isset($name)) {
            throw new BusinessException(ResultCode::UNAUTHORIZED);
        }
        $jwt = $this->jwtFactory->get($name);
        $token = $jwt->parser($this->getToken());

        RequestContext::set(RequestContext::get()->setAttribute('token', $token));

        // do something
        return $proceedingJoinPoint->process();
    }

    private function getToken(): string
    {
        $request = RequestContext::get();
        if ($request->hasHeader('Authorization')) {
            return str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));
        }
        if ($request->hasHeader('token')) {
            return $request->getHeaderLine('token');
        }
        if (isset($request->getQueryParams()['token'])) {
            return $request->getQueryParams()['token'];
        }
        return '';
    }
}
