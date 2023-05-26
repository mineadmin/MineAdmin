<?php

declare(strict_types=1);
namespace App\Setting\Service;

use App\Setting\Mapper\SettingGenerateTablesMapper;
use App\Setting\Model\SettingGenerateTables;
use App\System\Service\DataMaintainService;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
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
use Mine\Generator\VueSaveGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 业务生成信息表业务处理类
 * Class SettingGenerateTablesService
 * @package App\Setting\Service
 */
class SettingGenerateTablesService extends AbstractService
{
    /**
     * @var SettingGenerateTablesMapper
     */
    public $mapper;

    /**
     * @var DataMaintainService
     */
    protected DataMaintainService $dataMaintainService;

    /**
     * @var SettingGenerateColumnsService
     */
    protected SettingGenerateColumnsService $settingGenerateColumnsService;

    /**
     * @var ModuleService
     */
    protected ModuleService $moduleService;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * SettingGenerateTablesService constructor.
     * @param SettingGenerateTablesMapper $mapper
     * @param DataMaintainService $dataMaintainService
     * @param SettingGenerateColumnsService $settingGenerateColumnsService
     * @param ModuleService $moduleService
     * @param ContainerInterface $container
     */
    public function __construct(
        SettingGenerateTablesMapper $mapper,
        DataMaintainService $dataMaintainService,
        SettingGenerateColumnsService $settingGenerateColumnsService,
        ModuleService $moduleService,
        ContainerInterface $container
    )
    {
        $this->mapper = $mapper;
        $this->dataMaintainService = $dataMaintainService;
        $this->settingGenerateColumnsService = $settingGenerateColumnsService;
        $this->moduleService = $moduleService;
        $this->container = $container;
    }

    /**
     * 装载数据表
     * @param array $params
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loadTable(array $params): bool
    {
        // 非系统数据源，同步远程库的表结构到本地
        if ($params['source'] !== \Mine\Mine::getMineName()) {
            foreach ($params['names'] as $sourceName => $item) {
                if (! \Hyperf\Database\Schema\Schema::hasTable($item['name'])) {
                    $this->container->get(SettingDatasourceService::class)->syncRemoteTableStructToLocal((int)$params['source'], $item);
                }
            }
        }
        try {
            Db::beginTransaction();
            foreach ($params['names'] as $item) {
                $tableInfo = [
                    'table_name' => $item['name'],
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
            Db::commit();
            return true;
        } catch (\Throwable $e) {
            Db::rollBack();
            throw new MineException($e->getMessage(), 500);
        }
    }

    /**
     * 同步数据表
     * @param int $id
     * @return bool
     */
    #[Transaction]
    public function sync(int $id): bool
    {
        $table = $this->read($id);
        $columns = $this->dataMaintainService->getColumnList(
            str_replace(\Hyperf\Support\env('DB_PREFIX'), '', $table['table_name'])
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
     * 更新业务表
     * @param array $data
     * @return bool
     */
    #[Transaction]
    public function updateTableAndColumns(array $data): bool
    {
        $id = $data['id'];
        $columns = $data['columns'];

        unset($data['columns']);

        if (!empty($data['belong_menu_id'])) {
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
     * @param array $ids
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * 生成步骤
     * @param int $id
     * @param int $adminId
     * @return SettingGenerateTables
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function generateCodeFile(int $id, int $adminId): SettingGenerateTables
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
            if (get_class($class) == 'Mine\Generator\SqlGenerator'){
                $class->setGenInfo($model, $adminId)->generator();
            } else {
                $class->setGenInfo($model)->generator();
            }
        }

        return $model;
    }

    /**
     * 打包代码文件
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function packageCodeFile(): string
    {
        $fs = $this->container->get(Filesystem::class);
        $zipFileName = BASE_PATH. '/runtime/mineadmin.zip';
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
                $archive->addEmptyDir(str_replace(BASE_PATH. '/runtime/generate/', '', $directory));
                $files = $fs->files($directory);
                foreach ($files as $file) {
                    $archive->addFile(
                        $directory . '/' . $file->getFilename(),
                        str_replace(
                            BASE_PATH. '/runtime/generate/', '', $directory
                        ) . '/' . $file->getFilename()
                    );
                }
                $this->addZipFile($archive, $directory);
            }
        }
    }

    /**
     * 初始化生成设置
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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

    /**
     * 获取所有模型
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getModels(): array
    {
        $models = [];
        foreach ($this->moduleService->getModuleCache() as $item) if ($item['enabled']) {
            $path = sprintf("%s/app/%s/Model/*", BASE_PATH, $item['name']);
            foreach (glob($path) as $file) {
                $models[] = sprintf(
                    '\App\%s\Model\%s',
                    $item['name'],
                    str_replace('.php', '', basename($file))
                );
            }
        }

        return $models;
    }

    /**
     * 预览代码
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function preview(int $id): array
    {
        /** @var SettingGenerateTables $model */
        $model = $this->read($id);

        return [
            [
                'tab_name' => 'Controller.php',
                'name' => 'controller',
                'code' => make(ControllerGenerator::class)->setGenInfo($model)->preview(),
                'lang' => 'php'
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
}