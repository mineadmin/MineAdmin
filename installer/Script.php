<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Installer;

use Composer\Script\Event;

class Script
{
    public static function install(Event $event)
    {
        $io = $event->getIO();

        $io->write('<info>Setting up optional packages</info>');
        $languageAnswer = $io->select('What language do you want to setup ?', ['zh-CN', 'en-US'], 'zh-CN');
        $installer = new OptionalPackages($io, $event->getComposer(),$languageAnswer);
        $installer->setupRuntimeDir();
        $installer->selectDriver();
        $installer->updateRootPackage();
        $installer->removeInstallerFromDefinition();
        $installer->finalizePackage();

        $installer->setupDatabaseEnv();
        $installer->setupRedisEnv();
        $installer->generatorJwtSecret();
        $installer->putEnv();
    }
}