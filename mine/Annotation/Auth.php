<?php

namespace Mine\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 用户登录验证。
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
class Auth extends AbstractAnnotation
{
    /**
     * scene
     * @var string
     */
    public $scene;

    public function __construct($value = 'default')
    {
        parent::__construct($value);
        $this->bindMainProperty('scene', $value);
    }
}