<?php

namespace Installer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\Link;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class OptionalPackages
{
    private string $projectRoot;

    private JsonFile $composerJson;

    private array $composerDefinition;

    private RootPackageInterface $rootPackage;

    private array $composerRequires;

    private array $composerDevRequires;

    private array $stabilityFlags;

    private Translation $translation;

    private string $installerSource;

    private array $env = [
        'APP_NAME'  =>  'MineAdmin',
        'APP_ENV'   =>  'prod',
        'APP_DEBUG' =>  'false'
    ];

    /**
     * Assets to remove during cleanup.
     */
    private array $assetsToRemove = [
        '.travis.yml',
        '.coderabbit.yaml',
    ];

    public function __construct(public readonly IOInterface $io,public  readonly Composer $composer, ?string $language = 'zh-CN')
    {
        $composerFile = Factory::getComposerFile();
        // Calculate project root from composer.json, if necessary
        $this->projectRoot =  realpath(dirname($composerFile));
        $this->projectRoot = rtrim($this->projectRoot, '/\\') . '/';
        // Parse the composer.json
        $this->parseComposerDefinition($composer, $composerFile);
        $this->translation = new Translation();
        $this->translation->setLanguage($language);
        $this->installerSource = realpath(__DIR__).'/';
    }

    public function installHyperfScript()
    {
        $installHyperfScriptAsk = [
            $this->translation->trans('install_hyperf_script_0','What time zone do you want to setup ?'),
            $this->translation->trans('install_hyperf_script_1','Default time zone for php.ini'),
            $this->translation->trans('install_hyperf_script_2','Make your selection or type a time zone name, like Asia/Shanghai'),
            $this->translation->trans('install_hyperf_script_3','You should type a time zone name, like Asia/Shanghai. Or type n to skip.'),
        ];
        $ask[] = "\n  <question> ".$installHyperfScriptAsk[0]." </question>\n";
        $ask[] = "  [<comment>n</comment>] ".$installHyperfScriptAsk[1]."\n";
        $ask[] = $installHyperfScriptAsk[2]." (n):\n";
        $answer = $this->io->askAndValidate(
            implode('', $ask),
            function ($value) use ($installHyperfScriptAsk){
                if ($value === 'y' || $value === 'yes') {
                    throw new InvalidArgumentException($installHyperfScriptAsk[3]);
                }

                return trim($value);
            },
            null,
            'n'
        );

        if ($answer == 'n') {
            $answer = date_default_timezone_get();
        }

        $content = file_get_contents($this->installerSource . '/resources/bin/hyperf.stub');
        $content = str_replace('%TIME_ZONE%', $answer, $content);
        file_put_contents($this->projectRoot . '/bin/hyperf.php', $content);
    }


    /**
     * Create data and cache directories, if not present.
     *
     * Also sets up appropriate permissions.
     */
    public function setupRuntimeDir(): void
    {
        $str = $this->translation->trans('setup_runtime_directory','Setup data and cache dir');
        $this->io->write('<info>'. $str .'</info>');
        $runtimeDir = $this->projectRoot . '/runtime';

        if (! is_dir($runtimeDir)) {
            mkdir($runtimeDir, 0775, true);
            chmod($runtimeDir, 0775);
        }
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
     * Select Coroutine Driver
     */
    public function selectDriver(): void
    {
        $swooleInstalled = extension_loaded('swoole');
        $swowInstalled = extension_loaded('swow');
        if (! $swooleInstalled && ! $swowInstalled){
            $this->io->error($this->translation->trans('driver_not_found_1','Please install Swoole or Swow extension first'));
            exit;
        }
        if ($swowInstalled && $swooleInstalled) {
            $this->io->write('<info>' . $this->translation->trans('select_driver', 'Select Coroutine Driver') . '</info>');
            $driver = $this->io->select(
                $this->translation->trans('select_driver_0', 'What coroutine driver do you want to setup ?'),
                [
                    'swoole' => 'Swoole',
                    'swow' => 'Swow',
                ],
                'swow'
            );
        }else{
            $driver = $swowInstalled ? 'swow' : 'swoole';
        }

        if ($driver === 'swow'){
            $swow = [
                __DIR__.'/resouces/swow/hyperf.php' => $this->projectRoot.'/bin/hyperf.php',
                __DIR__.'/resouces/swow/server.php' => $this->projectRoot.'/config/autoload/server.php',
                __DIR__.'/resouces/swow/bootstrap.php' => $this->projectRoot.'/tests/bootstrap.php'
            ];
            foreach ($swow as $source => $destination) {
                if (file_exists($destination)) {
                    unlink($destination);
                }
                copy($source, $destination);
            }
            $this->io->warning($this->translation->trans('swow_timezone_warning','You have selected the Swow coroutine driver, please reconfigure the timezone in bin/hyperf.php'));
        }

        $this->composerRequires['ext-'.$driver] = '*';
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
     * Removes composer.lock file from gitignore.
     *
     * @codeCoverageIgnore
     */
    private function clearComposerLockFile(): void
    {
        $this->io->write('<info>Removing composer.lock from .gitignore</info>');
        $ignoreFile = sprintf('%s/.gitignore', $this->projectRoot);
        $content = $this->removeLinesContainingStrings(['composer.lock'], file_get_contents($ignoreFile));
        file_put_contents($ignoreFile, $content);
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
        $rdi = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::CHILD_FIRST);
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
        $this->recursiveRmdir($this->installerSource);
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
        $this->clearComposerLockFile();
        $this->cleanUp();
    }

    public function setupDatabaseEnv(): void
    {
        $this->io->write('<info>'. $this->translation->trans('setup_database_env','Setup database connection') .'</info>' .PHP_EOL);
        $databaseType = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_0','Please select database type (mysql,pgsql)') .'</info>' .PHP_EOL,
            'mysql'
        );
        $this->io->write('<info>' . $databaseType . '</info>'.PHP_EOL);
        $databaseHost = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_1','Please input database connection address(default: 127.0.0.1)') .'</info>' .PHP_EOL,
            '127.0.0.1'
        );
        $this->io->write('<info>' . $databaseHost . '</info>'.PHP_EOL);
        $databasePort = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_2','Please input database port(default: 3306)') .'</info>' .PHP_EOL,
            '3306'
        );
        $this->io->write('<info>' . $databasePort . '</info>'.PHP_EOL);
        $databaseUser = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_3','Please input database user name(default: root)') .'</info>' .PHP_EOL,
            'root'
        );
        $this->io->write('<info>' . $databaseUser . '</info>'.PHP_EOL);
        $databasePassword = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_4','Please input database password (default: \'\')') .'</info>' .PHP_EOL,
            ''
        );
        $this->io->write('<info>' . $databasePassword . '</info>'.PHP_EOL);
        $databaseName = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_5','Please input database name (default: mineadmin)') .'</info>' .PHP_EOL,
            'mineadmin'
        );
        $this->io->write('<info>' . $databaseName . '</info>'.PHP_EOL);
        $databaseCharset = $this->io->ask(
            '<info>'. $this->translation->trans('setup_database_env_6','Please input database charset (default: utf8mb4)') .'</info>' .PHP_EOL,
            'utf8mb4'
        );
        $this->io->write('<info>' . $databaseCharset . '</info>'.PHP_EOL);
        // test database connection
        $this->io->write('<info>'. $this->translation->trans('setup_database_env_7','Testing database connection') .'</info>'.PHP_EOL) ;
        try {
            $pdo = new \PDO(
                "{$databaseType}:host={$databaseHost};port={$databasePort};dbname={$databaseName};charset={$databaseCharset}",
                $databaseUser,
                $databasePassword
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stm = $pdo->query("SELECT 1");
            $res = $stm->fetch();
            if (!$res) {
                $this->io->write('<error>'. $this->translation->trans('setup_database_env_9','Database connection test failed') .'</error>'.PHP_EOL);
                exit;
            }else{
                $this->io->write('<info>'. $this->translation->trans('setup_database_env_8','Database connection test successful') .'</info>'.PHP_EOL);
            }
        }catch (\Exception $e){
            $this->io->write('<error>'. $e->getMessage() .'</error>');
            $this->io->write('<error>'. $this->translation->trans('setup_database_env_9','Database connection test failed') .'</error>'.PHP_EOL);
            exit(1);
        }
        $this->env['DB_DRIVER'] = $databaseType;
        $this->env['DB_HOST'] = $databaseHost;
        $this->env['DB_PORT'] = $databasePort;
        $this->env['DB_DATABASE'] = $databaseName;
        $this->env['DB_USERNAME'] = $databaseUser;
        $this->env['DB_PASSWORD'] = $databasePassword;
        $this->env['DB_CHARSET'] = $databaseCharset;

    }

    public function setupRedisEnv(): void
    {
        $this->io->write('<info>'. $this->translation->trans('setup_redis_env','Setup redis connection') .'</info>');
        $redisHost = $this->io->ask(
            '<info>'.$this->translation->trans('setup_redis_env_0','Please input redis connection address(default: 127.0.0.1)'.'</info>'.PHP_EOL),
            '127.0.0.1'
        );
        $this->io->write('<info>' . $redisHost . '</info>'.PHP_EOL);
        $redisPort = $this->io->ask(
            '<info>'.$this->translation->trans('setup_redis_env_1','Please input redis port(default: 6379)' .'</info>'.PHP_EOL),
            '6379'
        );
        $this->io->write('<info>' . $redisPort . '</info>'.PHP_EOL);
        $redisPassword = $this->io->ask(
            '<info>'.$this->translation->trans('setup_redis_env_2','Please input redis password (default: Empty)'.'</info>'.PHP_EOL),
            ''
        );
        $this->io->write('<info>' . $redisPassword . '</info>'.PHP_EOL);
        $redisDb = $this->io->ask(
            '<info>'.$this->translation->trans('setup_redis_env_3','Please input redis db (default: 0)'.'</info>'.PHP_EOL),
            '0'
        );
        $this->io->write('<info>' . $redisDb . '</info>'.PHP_EOL);
        // test redis connection

        $this->io->write('<info>'. $this->translation->trans('setup_redis_env_4','Testing redis connection') .'</info>'.PHP_EOL);

        try {
            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);
            if ($redisPassword) {
                $redis->auth($redisPassword);
            }
            $redis->select((int)$redisDb);
            $redis->ping();
            $this->io->write('<info>'. $this->translation->trans('setup_redis_env_5','Redis connection test successful') .'</info>'.PHP_EOL);
        }catch (\Exception $e){
            $this->io->write('<error>'. $e->getMessage() .'</error>'.PHP_EOL);
            $this->io->write('<error>'. $this->translation->trans('setup_redis_env_6','Redis connection test failed') .'</error>'.PHP_EOL);
            exit(1);
        }
        $this->env['REDIS_HOST'] = $redisHost;
        $this->env['REDIS_AUTH'] = $redisPassword;
        $this->env['REDIS_PORT'] = $redisPort;
        $this->env['REDIS_DB'] = $redisDb;
    }

    public function generatorJwtSecret(): void
    {
        $value = base64_encode(random_bytes(32));
        $this->env['JWT_SECRET'] = $value;
        $this->io->write('<info>'. $this->translation->trans('generator_jwt_secret','JWT_SECRET') .'</info>');
    }

    public function putEnv(): void
    {
        $envFile = $this->rootPath . '/.env';
        if (file_exists($envFile)) {
            $this->io->error($this->translation->trans('put_env_file_exists','The .env file already exists.'));
            // 已经存在环境文件，请手动填写以下配置项
            foreach ($this->env as $key => $value){
                $this->io->write('<info>'. $key .'=>'. $value.'</info>');
            }
            $this->io->write('<info>'. $this->translation->trans('put_env_file_exists_2','Please manually fill in the following configuration items') .'</info>');
            return;
        }
        $content = "APP_NAME=\"{$this->env['APP_NAME']}\"\n";
        $content .= "APP_ENV={$this->env['APP_ENV']}\n";
        $content .= "APP_DEBUG={$this->env['APP_DEBUG']}\n";
        $content .= "DB_DRIVER={$this->env['DB_DRIVER']}\n";
        $content .= "DB_HOST={$this->env['DB_HOST']}\n";
        $content .= "DB_PORT={$this->env['DB_PORT']}\n";
        $content .= "DB_DATABASE={$this->env['DB_DATABASE']}\n";
        $content .= "DB_USERNAME={$this->env['DB_USERNAME']}\n";
        $content .= "DB_PASSWORD={$this->env['DB_PASSWORD']}\n";
        $content .= "DB_CHARSET={$this->env['DB_CHARSET']}\n";
        $content .= "REDIS_HOST={$this->env['REDIS_HOST']}\n";
        $content .= "REDIS_AUTH={$this->env['REDIS_AUTH']}\n";
        $content .= "REDIS_PORT={$this->env['REDIS_PORT']}\n";
        $content .= "REDIS_DB={$this->env['REDIS_DB']}\n";
        $content .= "JWT_SECRET={$this->env['JWT_SECRET']}\n";
        file_put_contents($envFile, $content);

    }
}