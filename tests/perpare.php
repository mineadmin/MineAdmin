<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin-cloud.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  death_satan@qq.com
 * @license  Apache2.0
 */
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\ClassLoader;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;

require_once __DIR__ . '/../vendor/autoload.php';

defined('BASE_PATH') or define('BASE_PATH', dirname(__DIR__, 1));

(function () {
    ClassLoader::init();

    ApplicationContext::setContainer(
        new Container((new DefinitionSourceFactory())())
    );

    //     $container->get(Hyperf\Contract\ApplicationInterface::class);
})();
