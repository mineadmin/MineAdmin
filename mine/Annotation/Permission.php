<?php

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