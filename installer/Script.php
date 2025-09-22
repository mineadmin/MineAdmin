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
    public static function installBefore(Event $event)
    {
        $io = $event->getIO();
        $languageAnswer = $io->select('<info>What language do you want to setup ?</info>', ['zh-CN', 'en-US'], 'zh-CN');
        $languageAnswer = match ($languageAnswer){
            "en-US", "1" => "en_US",
            default => "zh-CN"
        };

        // 保存选择语言
        $languageFile = __DIR__ . '/resources/language/selected_language.php';
        file_put_contents($languageFile, "<?php\n return '" . $languageAnswer . "';\n");
        $installer = new OptionalPackages($io, $event->getComposer(),$languageAnswer);
        $installer->installHyperfScript();
        $installer->setupRuntimeDir();
        $installer->selectDriver();
        $installer->setupDatabaseEnv();
        $installer->setupRedisEnv();
        $installer->generatorJwtSecret();
        $installer->putEnv();
        $installer->removeInstallerFromDefinition();
        $installer->updateRootPackage();
        $installer->finalizePackage();
    }

    public static function installAfter(Event $event)
    {
        $io = $event->getIO();
        // Read selected language
        $languageFile = __DIR__ . '/resources/language/selected_language.php';
        if (file_exists($languageFile)) {
            $languageAnswer = include $languageFile;
        } else {
            $languageAnswer = 'zh-CN';
        }
        $installer = new OptionalPackages($io, $event->getComposer(), $languageAnswer);
        $installer->migrateAndSeed();
        $installer->cleanupInstaller();
    }
}