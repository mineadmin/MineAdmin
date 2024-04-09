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

namespace App\Setting\Service;

use App\Setting\Mapper\SettingGenerateTablesMapper;
use App\Setting\Model\SettingGenerateTables;
use App\System\Service\DataMaintainService;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Annotation\Transactional as Transaction;
use Hyperf\Support\Filesystem\Filesystem;
use Hyperf\Validation\ValidatorFactory;
use Mine\Abstracts\AbstractService;
use Mine\Exception\MineException;
use Mine\Generator\ApiGenerator;
use Mine\Generator\ControllerGenerator;
use Mine\Generator\DtoGenerator;
use Mine\Generator\MapperGenerator;
use Mine\Generator\ModelGenerator;
use Mine\Generator\RequestGenerator;
use Mine\Generator\ServiceGenerator;
use Mine\Generator\SqlGenerator;
use Mine\Generator\VueIndexGenerator;
use Mine\Mine;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 业务生成信息表业务处理类
 * Class SettingGenerateTablesService.
 */
class SettingGenerateTablesService extends AbstractService
{
    /**
     * @var SettingGenerateTablesMapper
     */
    public $mapper;

    protected DataMaintainService $dataMaintainService;

    protected SettingGenerateColumnsService $settingGenerateColumnsService;

    protected ModuleService $moduleService;

    protected ContainerInterface $container;

    /**
     * SettingGenerateTablesService constructor.
     */
    public function __construct(
        SettingGenerateTablesMapper $mapper,
        DataMaintainService $dataMaintainService,
        SettingGenerateColumnsService $settingGenerateColumnsService,
        ModuleService $moduleService,
        ContainerInterface $container,
        private readonly ValidatorFactory $validatorFactory
    ) {
        $this->mapper = $mapper;
        $this->dataMaintainService = $dataMaintainService;
        $this->settingGenerateColumnsService = $settingGenerateColumnsService;
        $this->moduleService = $moduleService;
        $this->container = $container;
    }

    /**
     * 装载数据表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transaction]
    public function loadTable(array $params): bool
    {
        // 非系统数据源，同步远程库的表结构到本地
        if ($params['source'] !== Mine::getMineName()) {
            foreach ($params['names'] as $sourceName => $item) {
                if (! Schema::hasTable($item['name'])) {
                    $this->container->get(SettingDatasourceService::class)->syncRemoteTableStructToLocal((int) $params['source'], $item);
                }
            }
        }
        if (! is_array($params['names'][0] ?? null)) {
            throw new MineException(t('setting.names_type_error'));
        }
        foreach ($params['names'] as $item) {
            $this->validatorFactory->validate(
                data: $item,
                rules: [
                    'name' => 'required|string',
                    'comment' => 'required|string',
                ]
            );
            $tableInfo = [
                'table_name' => env('DB_PREFIX') ? str_replace(env('DB_PREFIX'), '', $item['name']) : $item['name'],
                'table_comment' => $item['comment'],
                'menu_name' => $item['comment'],
                'type' => 'single',
            ];
            $id = $this->save($tableInfo);

            $columns = $this->dataMaintainService->getColumnList($item['name']);

            foreach ($columns as &$column) {
                $column['table_id'] = $id;
            }
            $this->settingGenerateColumnsService->save($columns);
        }
        return true;
    }

    /**
     * 同步数据表.
     */
    #[Transaction]
    public function sync(mixed $id): bool
    {
        $table = $this->read($id);
        $columns = $this->dataMaintainService->getColumnList(
            str_replace(env('DB_PREFIX'), '', $table['table_name'])
        );
        $model = $this->settingGenerateColumnsService->mapper->getModel();
        $ids = $model->newQuery()->where('table_id', $table['id'])->pluck('id');

        $this->settingGenerateColumnsService->mapper->delete($ids->toArray());
        foreach ($columns as &$column) {
            $column['table_id'] = $id;
        }
        $this->settingGenerateColumnsService->save($columns);
        return true;
    }

    /**
     * 更新业务表.
     */
    #[Transaction]
    public function updateTableAndColumns(array $data): bool
    {
        $id = $data['id'];
        $columns = $data['columns'];

        unset($data['columns']);

        if (! empty($data['belong_menu_id'])) {
            $data['belong_menu_id'] = is_array($data['belong_menu_id']) ? array_pop($data['belong_menu_id']) : $data['belong_menu_id'];
        } else {
            $data['belong_menu_id'] = 0;
        }

        $data['package_name'] = empty($data['package_name']) ? null : ucfirst($data['package_name']);
        $data['namespace'] = "App\\{$data['module_name']}";
        $data['generate_menus'] = implode(',', $data['generate_menus']);

        if (empty($data['options'])) {
            unset($data['options']);
        }

        // 更新业务表
        $this->update($id, $data);

        // 更新业务字段表
        foreach ($columns as $column) {
            $this->settingGenerateColumnsService->update($column['id'], $column);
        }
        return true;
    }

    /**
     * 生成代码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function generate(array $ids): string
    {
        $this->initGenerateSetting();
        $adminId = user()->getId();
        foreach ($ids as $id) {
            $this->generateCodeFile((int) $id, $adminId);
        }

        return $this->packageCodeFile();
    }

    /**
     * 获取所有模型.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getModels(): array
    {
        $models = [];
        foreach ($this->moduleService->getModuleCache() as $item) {
            if ($item['enabled']) {
                $path = sprintf('%s/app/%s/Model/*', BASE_PATH, $item['name']);
                foreach (glob($path) as $file) {
                    $models[] = sprintf(
                        '\App\%s\Model\%s',
                        $item['name'],
                        str_replace('.php', '', basename($file))
                    );
                }
            }
        }

        return $models;
    }

    /**
     * 预览代码
     * @throws \Exception
     */
    public function preview(mixed $id): array
    {
        /** @var SettingGenerateTables $model */
        $model = $this->read($id);

        if (empty($model)) {
            throw new MineException(t('setting.preview.not_found_table'));
        }

        return [
            [
                'tab_name' => 'Controller.php',
                'name' => 'controller',
                'code' => make(ControllerGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php',
            ],
            [
                'tab_name' => 'Model.php',
                'name' => 'model',
                'code' => make(ModelGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php',
            ],
            [
                'tab_name' => 'Service.php',
                'name' => 'service',
                'code' => make(ServiceGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php',
            ],
            [
                'tab_name' => 'Mapper.php',
                'name' => 'mapper',
                'code' => make(MapperGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php',
            ],
            [
                'tab_name' => 'Request.php',
                'name' => 'request',
                'code' => make(RequestGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php',
            ],
            [
                'tab_name' => 'Dto.php',
                'name' => 'dto',
                'code' => make(DtoGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php',
            ],
            [
                'tab_name' => 'Api.js',
                'name' => 'api',
                'code' => make(ApiGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'javascript',
            ],
            [
                'tab_name' => 'Index.vue',
                'name' => 'index',
                'code' => make(VueIndexGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'html',
            ],
            [
                'tab_name' => 'Menu.sql',
                'name' => 'sql',
                'code' => make(SqlGenerator::class)->setGenInfo($model, user()->getId())->preview(),
                'lang' => 'mysql',
            ],
        ];
    }

    /**
     * 生成步骤.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function generateCodeFile(mixed $id, int $adminId): SettingGenerateTables
    {
        /** @var SettingGenerateTables $model */
        $model = $this->read($id);

        $classList = [
            ControllerGenerator::class,
            ModelGenerator::class,
            ServiceGenerator::class,
            MapperGenerator::class,
            RequestGenerator::class,
            ApiGenerator::class,
            VueIndexGenerator::class,
            SqlGenerator::class,
            DtoGenerator::class,
        ];

        foreach ($classList as $cls) {
            $class = make($cls);
            if (get_class($class) == 'Mine\Generator\SqlGenerator') {
                $class->setGenInfo($model, $adminId)->generator();
            } else {
                $class->setGenInfo($model)->generator();
            }
        }

        return $model;
    }

    /**
     * 打包代码文件.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function packageCodeFile(): string
    {
        $fs = $this->container->get(Filesystem::class);
        $zipFileName = BASE_PATH . '/runtime/mineadmin.zip';
        $path = BASE_PATH . '/runtime/generate';
        // 删除老的压缩包
        @unlink($zipFileName);
        $archive = new \ZipArchive();
        $archive->open($zipFileName, \ZipArchive::CREATE);
        $files = $fs->files($path);
        foreach ($files as $file) {
            $archive->addFile(
                $path . '/' . $file->getFilename(),
                $file->getFilename()
            );
        }
        $this->addZipFile($archive, $path);
        $archive->close();
        return $zipFileName;
    }

    protected function addZipFile(\ZipArchive $archive, string $path): void
    {
        $fs = $this->container->get(Filesystem::class);
        foreach ($fs->directories($path) as $directory) {
            if ($fs->isDirectory($directory)) {
                $archive->addEmptyDir(str_replace(BASE_PATH . '/runtime/generate/', '', $directory));
                $files = $fs->files($directory);
                foreach ($files as $file) {
                    $archive->addFile(
                        $directory . '/' . $file->getFilename(),
                        str_replace(
                            BASE_PATH . '/runtime/generate/',
                            '',
                            $directory
                        ) . '/' . $file->getFilename()
                    );
                }
                $this->addZipFile($archive, $directory);
            }
        }
    }

    /**
     * 初始化生成设置.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function initGenerateSetting(): void
    {
        // 设置生成目录
        $genDirectory = BASE_PATH . '/runtime/generate';
        $fs = $this->container->get(Filesystem::class);

        // 先删除再创建
        $fs->cleanDirectory($genDirectory);
        $fs->deleteDirectory($genDirectory);
    }
}
