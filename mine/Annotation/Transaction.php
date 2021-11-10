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

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 数据库事务注解。
 * @Annotation
 * @Target({"METHOD"})
 */
class Transaction extends AbstractAnnotation
{
    /**
     * retry 重试次数
     * @var int
     */
    public $retry = 1;

    public function __construct($value)
    {
        parent::__construct($value);
        $this->bindMainProperty('retry', $value);
    }
}