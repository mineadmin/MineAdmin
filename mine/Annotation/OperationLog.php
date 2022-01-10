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
 * 记录操作日志注解。
 * @Annotation
 * @Target({"METHOD"})
 */
class OperationLog extends AbstractAnnotation
{
    /**
     * 菜单名称
     * @var string
     */
    public $menuName;

    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->bindMainProperty('menuName', $value);
    }
}