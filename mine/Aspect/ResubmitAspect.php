<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\Resubmit;
use Mine\Exception\NormalStatusException;
use Mine\MineRequest;
use Mine\Redis\MineLockRedis;

/**
 * Class ResubmitAspect
 * @package Mine\Aspect
 */
#[Aspect]
class ResubmitAspect extends AbstractAspect
{

    public array $annotations = [
        Resubmit::class
    ];

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var $resubmit Resubmit*/
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class])) {
            $resubmit = $proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class];
        }

        $request = container()->get(MineRequest::class);

        $key = md5(sprintf('%s-%s-%s', $request->ip(), $request->getPathInfo(), $request->getMethod()));

        $lockRedis = new MineLockRedis();
        $lockRedis->setTypeName('resubmit');

        if ($lockRedis->check($key)) {
            $lockRedis = null;
            throw new NormalStatusException($resubmit->message ?: t('mineadmin.resubmit'), 500);
        }

        $lockRedis->lock($key, $resubmit->second);
        $lockRedis = null;

        return $proceedingJoinPoint->process();
    }
}