<?php

namespace Installer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Link;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;

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
        'APP_NAME' => 'MineAdmin',
        'APP_ENV' => 'prod',
        'APP_DEBUG' => 'false',
    ];

    /**
     * Assets to remove during cleanup.
     */
    private array $assetsToRemove = [
        '.travis.yml',
        '.coderabbit.yaml',
    ];

    /**
     * Deferred actions queue. Each item: ['desc' => string, 'action' => callable]
     */
    private array $deferredActions = [];

    public function __construct(public readonly IOInterface $io, public readonly Composer $composer, ?string $language = 'zh-CN')
    {
        $composerFile = Factory::getComposerFile();
        $this->projectRoot = realpath(\dirname($composerFile));
        $this->projectRoot = rtrim($this->projectRoot, '/\\') . '/';
        $this->parseComposerDefinition($composer, $composerFile);
        $this->translation = new Translation();
        $this->translation->setLanguage($language);
        $this->installerSource = realpath(__DIR__) . '/';
    }

    /**
     * Add a deferred action to the queue.
     */
    private function deferAction(string $description, callable $action): void
    {
        $this->deferredActions[] = [
            'desc' => $description,
            'action' => $action,
        ];
    }

    /**
     * Execute all deferred actions. If interactive is true, ask for confirmation.
     */
    public function executeDeferredActions(bool $interactive = true): void
    {
        if (empty($this->deferredActions)) {
            return;
        }

        $this->io->write("\n<info>" . $this->translation->trans('summary_actions', 'Please check if the configuration is correct:') . "</info>\n");

        foreach ($this->env as $key => $value) {
            $this->io->write('<comment>' . sprintf("  %s => %s", $key, $value) . '</comment>');
        }


        if ($interactive) {
            $confirm = $this->io->ask("\n<info>" . $this->translation->trans('confirm_proceed', 'Shall we continue? [y/n] (default: y)') . '</info>' . PHP_EOL, 'y');
            if (strtolower(trim($confirm)) !== 'y') {
                $this->io->write('<info>' . $this->translation->trans('action_cancelled', 'Action cancelled.') . '</info>');
                exit(1);
            }
        }

        foreach ($this->deferredActions as $item) {
            try {
                $callable = $item['action'];
                $callable();
                $this->io->write('<info>' . $this->translation->trans('action_done_single', 'Done:') . ' ' . $item['desc'] . '</info>');
            } catch (\Throwable $e) {
                $this->io->write('<error>' . $this->translation->trans('action_failed_single', 'Failed:') . ' ' . $item['desc'] . ' - ' . $e->getMessage() . '</error>');
                exit(1);
            }
        }

        // 清空队列
        $this->deferredActions = [];
    }

    public function installHyperfScript(): void
    {
        $installHyperfScriptAsk = [
            $this->translation->trans('install_hyperf_script_0', 'What time zone do you want to setup ?'),
            $this->translation->trans('install_hyperf_script_1', 'Default time zone for php.ini'),
            $this->translation->trans('install_hyperf_script_2', 'Make your selection or type a time zone name, like Asia/Shanghai'),
            $this->translation->trans('install_hyperf_script_3', 'You should type a time zone name, like Asia/Shanghai. Or type n to skip.'),
        ];
        $ask[] = "\n  <question> " . $installHyperfScriptAsk[0] . " </question>\n";
        $ask[] = '  [<comment>n</comment>] ' . $installHyperfScriptAsk[1] . "\n";
        $ask[] = $installHyperfScriptAsk[2] . " (n):\n";
        $answer = $this->io->askAndValidate(
            implode('', $ask),
            static function ($value) use ($installHyperfScriptAsk) {
                if ($value === 'y' || $value === 'yes') {
                    throw new \InvalidArgumentException($installHyperfScriptAsk[3]);
                }

                return trim($value);
            },
            null,
            'n'
        );

        if ($answer === 'n') {
            $answer = date_default_timezone_get();
        }

        $content = file_get_contents($this->installerSource . 'resources/bin/hyperf.stub');
        $content = str_replace('%TIME_ZONE%', $answer, $content);
        $dest = $this->projectRoot . '/bin/hyperf.php';

        $this->deferAction($this->translation->trans('action_write_file', 'Write file') . " {$dest}", function () use ($dest, $content) {
            if (!is_dir(dirname($dest))) {
                mkdir(dirname($dest), 0o775, true);
            }
            file_put_contents($dest, $content);
        });
    }

    /**
     * Create data and cache directories, if not present.
     *
     * Also sets up appropriate permissions.
     */
    public function setupRuntimeDir(): void
    {
        $str = $this->translation->trans('setup_runtime_directory', 'Setup data and cache dir');
        $this->io->write('<info>' . $str . '</info>');
        $runtimeDir = $this->projectRoot . '/runtime';

        // 延迟创建
        $this->deferAction($this->translation->trans('action_create_runtime_dir', 'Create runtime dir') . " {$runtimeDir}", function () use ($runtimeDir) {
            if (! is_dir($runtimeDir)) {
                mkdir($runtimeDir, 0o775, true);
                chmod($runtimeDir, 0o775);
            }
        });
    }

    /**
     * Update the root package based on current state.
     */
    public function updateRootPackage(): void
    {
        // 这部分是内存数据变更，保持即时（将在 finalize 时写入 composer.json）
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
        // 直接修改内存 composerDefinition（将在 finalize 写入）
        unset(
            $this->composerDefinition['autoload']['psr-4']['Installer\\'],
            $this->composerDefinition['autoload-dev']['psr-4']['InstallerTest\\'],
            $this->composerDefinition['extra']['branch-alias'],
            $this->composerDefinition['extra']['optional-packages'],
            $this->composerDefinition['scripts']['pre-update-cmd'],
            $this->composerDefinition['scripts']['pre-install-cmd']
        );
        // 清理安装器文件等放到清理队列（defer），以避免立即删除当前执行的代码文件
        $this->deferAction($this->translation->trans('action_cleanup_installer', 'Cleanup installer'), function () {
            $this->cleanUp();
        });
    }

    /**
     * Select Coroutine Driver.
     */
    public function selectDriver(): void
    {
        $swooleInstalled = \extension_loaded('swoole');
        $swowInstalled = \extension_loaded('swow');
        if (! $swooleInstalled && ! $swowInstalled) {
            $this->io->error($this->translation->trans('driver_not_found_1', 'Please install Swoole or Swow extension first'));
            exit(1);
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
        } else {
            $driver = $swowInstalled ? 'swow' : 'swoole';
            $this->io->write('<info>' . (
                $swowInstalled
                    ? $this->translation->trans('select_driver_single_swow', 'You have only installed the Swow extension, the coroutine driver has been set to Swow')
                    : $this->translation->trans('select_driver_single_swoole', 'You have only installed the Swoole extension, the coroutine driver has been set to Swoole')
                ) . '</info>');
        }

        // 仅记录动作（延迟执行）
        if ($driver === 'swow') {
            $files = [
                __DIR__ . '/resources/swow/hyperf.php' => $this->projectRoot . '/bin/hyperf.php',
                __DIR__ . '/resources/swow/server.php' => $this->projectRoot . '/config/autoload/server.php',
                __DIR__ . '/resources/swow/bootstrap.php' => $this->projectRoot . '/tests/bootstrap.php',
            ];

            $this->deferAction($this->translation->trans('action_copy_files', 'Copy files for swow'), function () use ($files) {
                foreach ($files as $source => $destination) {
                    if (file_exists($destination)) {
                        unlink($destination);
                    }
                    if (!is_dir(dirname($destination))) {
                        mkdir(dirname($destination), 0o775, true);
                    }
                    copy($source, $destination);
                    // 运行时输出
                    $this->io->info($source . ' ======> ' . $destination);
                }
            });

            // 提醒时区调整：保留为即时输出，但不修改文件
            $this->io->warning($this->translation->trans('swow_timezone_warning', 'You have selected the Swow coroutine driver, please reconfigure the timezone in bin/hyperf.php'));

            // 添加 composer package 的延迟动作（修改内存 composerDefinition）
            $this->deferAction($this->translation->trans('action_add_package', 'Add composer package: hyperf/engine-swow (~2.14)'), function () {
                $swowVersionParser = new VersionParser();
                $swowConstraint = $swowVersionParser->parseConstraints('~2.14');
                $link = new Link('__root__', 'hyperf/engine-swow', $swowConstraint, 'requires', '*');
                $this->composerDefinition['require'][$link->getTarget()] = '*';
                $this->composerRequires[$link->getTarget()] = $link;
            });
        } else {
            // swoole 情况下只记录 ext 要求
        }

        // 一律记录 ext 依赖的变更（延迟修改内存 composerDefinition）
        $this->deferAction($this->translation->trans('action_add_ext', 'Add ext requirement').'ext-'.$driver, function () use ($driver) {
            $ext = 'ext-' . $driver;
            $require = $this->composerDefinition['require'];

            if (array_key_exists('php', $require)) {
                $keys = array_keys($require);
                $phpIndex = array_search('php', $keys, true);
                $before = array_slice($require, 0, $phpIndex + 1, true);
                $after = array_slice($require, $phpIndex + 1, null, true);
                $newRequire = $before + [$ext => '*'] + $after;
            } else {
                $newRequire = [$ext => '*'] + $require;
            }

            $this->composerDefinition['require'] = $newRequire;
        });

    }

    /**
     * Remove lines from string content containing words in array.
     */
    public function removeLinesContainingStrings(array $entries, string $content): string
    {
        $entries = implode('|', array_map(static function ($word) {
            return preg_quote($word, '/');
        }, $entries));
        return preg_replace('/^.*(?:' . $entries . ").*$(?:\r?\n)?/m", '', $content);
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
        // 先执行所有延迟动作（包含文件复制、目录创建、composerDefinition 变更等）
        $this->executeDeferredActions(true);

        // Update composer definition
        $this->composerJson->write($this->composerDefinition);
        $this->clearComposerLockFile();
        $this->cleanUp();
    }

    public function setupDatabaseEnv(): void
    {
        $this->io->write('<info>' . $this->translation->trans('setup_database_env', 'Setup database connection') . '</info>' . \PHP_EOL);
        $databaseType = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_0', 'Please select database type (mysql,pgsql)') . '</info>' . \PHP_EOL,
            'mysql'
        );
        $this->io->write('<info>' . $databaseType . '</info>' . \PHP_EOL);
        $databaseHost = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_1', 'Please input database connection address (default: 127.0.0.1)') . '</info>' . \PHP_EOL,
            '127.0.0.1'
        );
        $this->io->write('<info>' . $databaseHost . '</info>' . \PHP_EOL);
        $databasePort = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_2', 'Please input database port (default: 3306)') . '</info>' . \PHP_EOL,
            '3306'
        );
        $this->io->write('<info>' . $databasePort . '</info>' . \PHP_EOL);
        $databaseUser = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_3', 'Please input database user name (default: root)') . '</info>' . \PHP_EOL,
            'root'
        );
        $this->io->write('<info>' . $databaseUser . '</info>' . \PHP_EOL);
        $databasePassword = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_4', 'Please input database password (default: \'\')') . '</info>' . \PHP_EOL,
            ''
        );
        $this->io->write('<info>' . $databasePassword . '</info>' . \PHP_EOL);
        $databaseName = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_5', 'Please input database name (default: mineadmin)') . '</info>' . \PHP_EOL,
            'mineadmin'
        );
        $this->io->write('<info>' . $databaseName . '</info>' . \PHP_EOL);
        $databaseCharset = $this->io->ask(
            '<info>' . $this->translation->trans('setup_database_env_6', 'Please input database charset (default: utf8mb4)') . '</info>' . \PHP_EOL,
            'utf8mb4'
        );
        $this->io->write('<info>' . $databaseCharset . '</info>' . \PHP_EOL);
        // test database connection
        $this->io->write('<info>' . $this->translation->trans('setup_database_env_7', 'Testing database connection') . '</info>' . \PHP_EOL);
        try {
            $pdo = new \PDO(
                "{$databaseType}:host={$databaseHost};port={$databasePort};dbname={$databaseName};charset={$databaseCharset}",
                $databaseUser,
                $databasePassword
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stm = $pdo->query('SELECT 1');
            $res = $stm->fetch();
            if (! $res) {
                $this->io->write('<error>' . $this->translation->trans('setup_database_env_9', 'Database connection test failed') . '</error>' . \PHP_EOL);
                exit(1);
            }
            $this->io->write('<info>' . $this->translation->trans('setup_database_env_8', 'Database connection test successful') . '</info>' . \PHP_EOL);

            // 新增：检查数据库是否为空（有没有表）
            $tables = [];
            if (strtolower($databaseType) === 'mysql') {
                $stmt = $pdo->query('SHOW TABLES');
                $tables = $stmt->fetchAll(\PDO::FETCH_NUM);
            } elseif (in_array(strtolower($databaseType), ['pgsql', 'postgres', 'postgresql'], true)) {
                $stmt = $pdo->prepare("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname NOT IN ('pg_catalog','information_schema')");
                $stmt->execute();
                $tables = $stmt->fetchAll(\PDO::FETCH_NUM);
            } else {
                // 通用 fallback：use information_schema if available
                try {
                    $stmt = $pdo->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()");
                    $stmt->execute();
                    $tables = $stmt->fetchAll(\PDO::FETCH_NUM);
                } catch (\Throwable $e) {
                    // 无法检测则忽略，不阻塞安装
                    $tables = [];
                }
            }

            if (!empty($tables)) {
                // 列出部分表并退出安装
                $names = array_map(static fn($row) => (is_array($row) ? $row[0] : (string)$row), $tables);
                $this->io->error($this->translation->trans('database_not_clean', 'Database is not empty. Please provide an empty database for installation.'));
                $this->io->write('<comment>' . $this->translation->trans('existing_tables', 'Existing tables:') . ' ' . implode(', ', array_slice($names, 0, 10)) . '</comment>' . \PHP_EOL);
                exit(1);
            }

        } catch (\Exception $e) {
            $this->io->write('<error>' . $e->getMessage() . '</error>');
            $this->io->write('<error>' . $this->translation->trans('setup_database_env_9', 'Database connection test failed') . '</error>' . \PHP_EOL);
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
        $this->io->write('<info>' . $this->translation->trans('setup_redis_env', 'Setup redis connection') . '</info>' . \PHP_EOL);
        $redisHost = $this->io->ask(
            '<info>' . $this->translation->trans('setup_redis_env_0', 'Please input redis connection address(default: 127.0.0.1)') . '</info>' . \PHP_EOL,
            '127.0.0.1'
        );
        $this->io->write('<info>' . $redisHost . '</info>' . \PHP_EOL);
        $redisPort = $this->io->ask(
            '<info>' . $this->translation->trans('setup_redis_env_1', 'Please input redis port(default: 6379)') . '</info>' . \PHP_EOL,
            '6379'
        );
        $this->io->write('<info>' . $redisPort . '</info>' . \PHP_EOL);
        $redisPassword = $this->io->ask(
            '<info>' . $this->translation->trans('setup_redis_env_2', 'Please input redis password (default: Empty)') . '</info>' . \PHP_EOL,
            ''
        );
        $this->io->write('<info>' . $redisPassword . '</info>' . \PHP_EOL);
        $redisDb = $this->io->ask(
            '<info>' . $this->translation->trans('setup_redis_env_3', 'Please input redis db (default: 0)') . '</info>' . \PHP_EOL,
            '0'
        );
        $this->io->write('<info>' . $redisDb . '</info>' . \PHP_EOL);
        // test redis connection

        $this->io->write('<info>' . $this->translation->trans('setup_redis_env_4', 'Testing redis connection') . '</info>' . \PHP_EOL);

        try {
            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);
            if ($redisPassword) {
                $redis->auth($redisPassword);
            }
            $redis->select((int) $redisDb);
            $redis->ping();
            $this->io->write('<info>' . $this->translation->trans('setup_redis_env_5', 'Redis connection test successful') . '</info>' . \PHP_EOL);
        } catch (\Exception $e) {
            $this->io->write('<error>' . $e->getMessage() . '</error>' . \PHP_EOL);
            $this->io->write('<error>' . $this->translation->trans('setup_redis_env_6', 'Redis connection test failed') . '</error>' . \PHP_EOL);
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
        $this->io->write('<info>' . $this->translation->trans('generator_jwt_secret', 'JWT_SECRET') . '</info>');
    }

    public function putEnv(): void
    {
        $envFile = $this->projectRoot . '/.env';
        if (file_exists($envFile)) {
            $this->io->error($this->translation->trans('put_env_file_exists', 'The .env file already exists.'));
            // 已经存在环境文件，请手动填写以下配置项
            $this->io->write('<info>' . $this->translation->trans('put_env_file_exists_2', 'Please manually fill in the following configuration items') . '</info>');
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

        // 延迟写入 .env
        $this->deferAction($this->translation->trans('action_write_env', 'Write .env file'), function () use ($envFile, $content) {
            file_put_contents($envFile, $content);
        });
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
     * Removes composer.lock file from gitignore.
     *
     * @codeCoverageIgnore
     */
    private function clearComposerLockFile(): void
    {
        $this->io->write('<info>Removing composer.lock from .gitignore</info>');
        $ignoreFile = \sprintf('%s/.gitignore', $this->projectRoot);
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
     * Clean up/remove installer classes and assets.
     *
     * On completion of install/update, removes the installer classes (including
     * this one) and assets (including configuration and templates).
     *
     * @codeCoverageIgnore
     */
    private function cleanUp(): void
    {
        foreach ($this->assetsToRemove as $target) {
            $target = $this->projectRoot . $target;
            if (file_exists($target)) {
                unlink($target);
            }
        }
        $this->recursiveRmdir($this->installerSource);
    }
}