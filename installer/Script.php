<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Installer;

use Composer\Script\Event;

class Script
{
    public static function install(Event $event)
    {
        $installer = new OptionalPackages($event->getIO(), $event->getComposer());

        $installer->io->write('<info>Setting up optional packages</info>');

        $installer->setupRuntimeDir();
        $installer->removeDevDependencies();
        $installer->updateRootPackage();
        $installer->removeInstallerFromDefinition();
        $installer->finalizePackage();
    }
}
