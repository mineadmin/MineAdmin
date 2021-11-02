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
 * 用户权限验证。
 * @Annotation
 * @Target({"METHOD"})
 */
class Permission extends AbstractAnnotation {

    /**
     * 菜单代码
     * @var string
     */
    public $menuCode;

    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->bindMainProperty('menuCode', $value);
    }


}