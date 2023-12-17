<?php

namespace PHPSTORM_META{
    // Reflect
    override(\Psr\Container\ContainerInterface::get(0), map(['' => '@']));
    override(\Hyperf\Context\Context::get(0), map(['' => '@']));
    override(\make(0), map(['' => '@']));
    override(\di(0), map(['' => '@']));
    override(\Hyperf\Support\make(0), map(['' => '@']));
    override(\Hyperf\Support\optional(0), type(0));
    override(\Hyperf\Tappable\tap(0), type(0));
}

namespace Hyperf\Database\Model{
    class Builder{
        /**
         * @param int|null $userid
         * @return Builder
         */
        public function userDataScope(?int $userid = null)
        {

        }
    }
}