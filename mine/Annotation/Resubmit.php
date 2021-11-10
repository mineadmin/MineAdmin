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
 * 禁止重复提交
 * @Annotation
 * @Target({"METHOD"})
 */
class Resubmit extends AbstractAnnotation
{
    /**
     * second
     * @var int
     */
    public $second = 3;

    public function __construct($value)
    {
        parent::__construct($value);
        $this->bindMainProperty('second', $value);
    }
}