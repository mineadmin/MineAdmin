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

declare(strict_types = 1);
namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 数据库事务注解。
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Transaction extends AbstractAnnotation
{
    /**
     * retry 重试次数
     * @var int
     */
    public int $retry = 1;

    public function __construct($value = 1)
    {
        parent::__construct($value);
        $this->bindMainProperty('retry', [ $value ]);
    }
}