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

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\Link;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;

class OptionalPackages
{
    /**
     * @const string Regular expression for matching package name and version
     */
    public const PACKAGE_REGEX = '/^(?P<name>[^:]+\/[^:]+)([:]*)(?P<version>.*)$/';

    /**
     * @var IOInterface
     */
    public $io;

    /**
     * Assets to remove during cleanup.
     *
     * @var string[]
     */
    private $assetsToRemove = [
        '.travis.yml',
    ];

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var array
     */
    private $composerDefinition;

    /**
     * @var JsonFile
     */
    private $composerJson;

    /**
     * @var Link[]
     */
    private $composerRequires;

    /**
     * @var Link[]
     */
    private $composerDevRequires;

    /**
     * @var string[] Dev dependencies to remove after install is complete
     */
    private $devDependencies = [
        'composer/composer',
    ];

    /**
     * @var string path to this file
     */
    private $installerSource;

    private array $removeSources = [];

    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @var RootPackageInterface
     */
    private $rootPackage;

    /**
     * @var int[]
     */
    private $stabilityFlags;

    public function __construct(IOInterface $io, Composer $composer, ?string $projectRoot = null)
    {
        $this->io = $io;
        $this->composer = $composer;
        // Get composer.json location
        $composerFile = Factory::getComposerFile();
        // Calculate project root from composer.json, if necessary
        // Calculate project root from composer.json, if necessary
        $this->projectRoot = realpath(dirname($composerFile));

        // Parse the composer.json
        $this->parseComposerDefinition($composer, $composerFile);
        // Source path for this file
        $this->installerSource = realpath(__DIR__);
        $this->removeSources = [
            $this->installerSource,
            /*$this->projectRoot.'/.github',
            $this->projectRoot.'/.travis',*/
        ];
    }

    /**
     * Create data and cache directories, if not present.
     *
     * Also sets up appropriate permissions.
     */
    public function setupRuntimeDir(): void
    {
        $this->io->write('<info>Setup data and cache dir</info>');
        $runtimeDir = $this->projectRoot . '/runtime';

        if (! is_dir($runtimeDir)) {
            mkdir($runtimeDir, 0775, true);
            chmod($runtimeDir, 0775);
        }
    }

    /**
     * Cleanup development dependencies.
     *
     * The dev dependencies should be removed from the stability flags,
     * require-dev and the composer file.
     */
    public function removeDevDependencies(): void
    {
        $this->io->write('<info>Removing installer development dependencies</info>');
        foreach ($this->devDependencies as $devDependency) {
            unset($this->stabilityFlags[$devDependency], $this->composerDevRequires[$devDependency], $this->composerDefinition['require-dev'][$devDependency]);
        }
    }

    /**
     * Update the root package based on current state.
     */
    public function updateRootPackage(): void
    {
        $this->rootPackage->setRequires($this->composerRequires);
        $this->rootPackage->setDevRequires($this->composerDevRequires);
        $this->rootPackage->setStabilityFlags($this->stabilityFlags);
        $this->rootPackage->setAutoload($this->composerDefinition['autoload']);
        $this->rootPackage->setDevAutoload($this->composerDefinition['autoload-dev']);
        $this->rootPackage->setExtra($this->composerDefinition['extra'] ?? []);
    }

    /**
     * Remove the installer from the composer definition.
     */
    public function removeInstallerFromDefinition(): void
    {
        $this->io->write('<info>Remove installer</info>');
        // Remove installer script autoloading rules
        unset(
            $this->composerDefinition['autoload']['psr-4']['Installer\\'],
            $this->composerDefinition['autoload-dev']['psr-4']['InstallerTest\\'],
            $this->composerDefinition['extra']['branch-alias'],
            $this->composerDefinition['extra']['optional-packages'],
            $this->composerDefinition['scripts']['pre-update-cmd'],
            $this->composerDefinition['scripts']['pre-install-cmd']
        );
    }

    /**
     * Finalize the package.
     *
     * Writes the current JSON state to composer.json, clears the
     * composer.lock file, and cleans up all files specific to the
     * installer.
     *
     * @codeCoverageIgnore
     */
    public function finalizePackage(): void
    {
        // Update composer definition
        $this->composerJson->write($this->composerDefinition);
        $this->cleanUp();
    }

    /**
     * Process the answer of a question.
     *
     * @param bool|int|string $answer
     */
    public function processAnswer(array $question, $answer): bool
    {
        if (isset($question['options'][$answer])) {
            // Add packages to install
            if (isset($question['options'][$answer]['packages'])) {
                foreach ($question['options'][$answer]['packages'] as $packageName) {
                    $packageData = $this->config['packages'][$packageName];
                    $this->addPackage($packageName, $packageData['version'], $packageData['whitelist'] ?? []);
                }
            }
            // Copy files
            if (isset($question['options'][$answer])) {
                $force = ! empty($question['force']);
                foreach ($question['options'][$answer]['resources'] as $resource => $target) {
                    $this->copyResource($resource, $target, $force);
                }
            }
            return true;
        }
        if ($question['custom-package'] === true && preg_match(self::PACKAGE_REGEX, (string) $answer, $match)) {
            $this->addPackage($match['name'], $match['version'], []);
            if (isset($question['custom-package-warning'])) {
                $this->io->write(sprintf('  <warning>%s</warning>', $question['custom-package-warning']));
            }
            return true;
        }
        return false;
    }

    /**
     * Add a package.
     */
    public function addPackage(string $packageName, string $packageVersion, array $whitelist = []): void
    {
        $this->io->write(sprintf(
            '  - Adding package <info>%s</info> (<comment>%s</comment>)',
            $packageName,
            $packageVersion
        ));
        // Get the version constraint
        $versionParser = new VersionParser();
        $constraint = $versionParser->parseConstraints($packageVersion);
        // Create package link
        $link = new Link('__root__', $packageName, $constraint, 'requires', $packageVersion);
        // Add package to the root package and composer.json requirements
        if (in_array($packageName, $this->config['require-dev'], true)) {
            unset($this->composerDefinition['require'][$packageName], $this->composerRequires[$packageName]);

            $this->composerDefinition['require-dev'][$packageName] = $packageVersion;
            $this->composerDevRequires[$packageName] = $link;
        } else {
            unset($this->composerDefinition['require-dev'][$packageName], $this->composerDevRequires[$packageName]);

            $this->composerDefinition['require'][$packageName] = $packageVersion;
            $this->composerRequires[$packageName] = $link;
        }
        // Set package stability if needed
        switch (VersionParser::parseStability($packageVersion)) {
            case 'dev':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_DEV;
                break;
            case 'alpha':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_ALPHA;
                break;
            case 'beta':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_BETA;
                break;
            case 'RC':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_RC;
                break;
        }
        // Whitelist packages for the component installer
        foreach ($whitelist as $package) {
            if (! in_array($package, $this->composerDefinition['extra']['zf']['component-whitelist'], true)) {
                $this->composerDefinition['extra']['zf']['component-whitelist'][] = $package;
                $this->io->write(sprintf('  - Whitelist package <info>%s</info>', $package));
            }
        }
    }

    /**
     * Copy a file to its final destination in the skeleton.
     *
     * @param string $resource resource file
     * @param string $target destination
     * @param bool $force whether or not to copy over an existing file
     */
    public function copyResource(string $resource, string $target, bool $force = false): void
    {
        // Copy file
        if ($force === false && is_file($this->projectRoot . $target)) {
            return;
        }
        $destinationPath = dirname($this->projectRoot . $target);
        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0775, true);
        }
        $this->io->write(sprintf('  - Copying <info>%s</info>', $target));
        copy($this->installerSource . $resource, $this->projectRoot . $target);
    }

    /**
     * Remove lines from string content containing words in array.
     */
    public function removeLinesContainingStrings(array $entries, string $content): string
    {
        $entries = implode('|', array_map(function ($word) {
            return preg_quote($word, '/');
        }, $entries));
        return preg_replace('/^.*(?:' . $entries . ").*$(?:\r?\n)?/m", '', $content);
    }

    /**
     * Clean up/remove installer classes and assets.
     *
     * On completion of install/update, removes the installer classes (including
     * this one) and assets (including configuration and templates).
     *
     * @codeCoverageIgnore
     */
    private function cleanUp(): void
    {
        $this->io->write('<info>Removing Expressive installer classes, configuration, tests and docs</info>');
        foreach ($this->assetsToRemove as $target) {
            $target = $this->projectRoot . $target;
            if (file_exists($target)) {
                unlink($target);
            }
        }
        foreach ($this->removeSources as $source){
            $this->recursiveRmdir($source);
        }
    }

    /**
     * Recursively remove a directory.
     *
     * @codeCoverageIgnore
     */
    private function recursiveRmdir(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }
        $rdi = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $rii = new \RecursiveIteratorIterator($rdi, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($rii as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($filename);
                continue;
            }
            unlink($filename);
        }
        rmdir($directory);
    }

    /**
     * Parses the composer file and populates internal data.
     */
    private function parseComposerDefinition(Composer $composer, string $composerFile): void
    {
        $this->composerJson = new JsonFile($composerFile);
        $this->composerDefinition = $this->composerJson->read();
        // Get root package or root alias package
        $this->rootPackage = $composer->getPackage();
        // Get required packages
        $this->composerRequires = $this->rootPackage->getRequires();
        $this->composerDevRequires = $this->rootPackage->getDevRequires();
        // Get stability flags
        $this->stabilityFlags = $this->rootPackage->getStabilityFlags();
    }
}
