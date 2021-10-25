<?php
declare(strict_types=1);
namespace Mine\Aspect;

use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

/**
 * Class TransactionAspect
 * @package Mine\Aspect
 * @Aspect
 */
class TransactionAspect extends AbstractAspect
{

    public $annotations = [
        Transaction::class
    ];

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        try {
            Db::beginTransaction();
            $result = $proceedingJoinPoint->process();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollBack();
            throw new NormalStatusException($e->getMessage(), 500);
        }
        return $result;
    }
}