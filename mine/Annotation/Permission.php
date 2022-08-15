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
 * 用户权限验证。
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Permission extends AbstractAnnotation {

    /**
     * 菜单代码
     * @var string
     */
    public string $code;

    /**
     * 多个菜单代码，过滤条件
     * 为 OR 时，检查有一个通过则全部通过
     * 为 AND 时，检查有一个不通过则全不通过
     * @var string
     */
    public string $where;

    public function __construct($value = null, $where = 'OR')
    {
        parent::__construct($value);
        $this->bindMainProperty('code', [ $value ]);
        $this->bindMainProperty('where', [ $where ]);
    }


}