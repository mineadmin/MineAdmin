<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Generator;

use App\Setting\Model\SettingGenerateColumns;
use App\Setting\Model\SettingGenerateTables;
use Hyperf\Database\Model\Collection;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;

class DtoGenerator extends MineGenerator implements CodeGenerator
{
    /**
     * @var SettingGenerateTables
     */
    protected SettingGenerateTables $model;

    /**
     * @var string
     */
    protected string $codeContent;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var Collection
     */
    protected Collection $columns;

    /**
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @return DtoGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): DtoGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);

        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)->orderByDesc('sort')
            ->get([ 'column_name', 'column_comment' ]);

        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $this->model->module_name[0] = Str::title($this->model->module_name[0]);
        $module = $this->model->module_name;
        if ($this->model->generate_type === 1) {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Dto/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Dto/";
        }
        $this->filesystem->exists($path) || $this->filesystem->makeDirectory($path, 0755, true, true);
        $this->filesystem->put($path . "{$this->getClassName()}.php", $this->replace()->getCodeContent());
    }

    /**
     * @return string
     */
    public function preview(): string
    {
        return $this->replace()->getCodeContent();
    }


    /**
     * 获取模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir().'/dto.stub';
    }

    /**
     * 读取模板内容
     * @return string
     */
    protected function readTemplate(): string
    {
        return $this->filesystem->sharedGet($this->getTemplatePath());
    }

    /**
     * 占位符替换
     */
    protected function placeholderReplace(): DtoGenerator
    {
        $this->setCodeContent(str_replace(
            $this->getPlaceHolderContent(),
            $this->getReplaceContent(),
            $this->readTemplate()
        ));

        return $this;
    }

    /**
     * 获取要替换的占位符
     */
    protected function getPlaceHolderContent(): array
    {
        return [
            '{NAMESPACE}',
            '{COMMENT}',
            '{CLASS_NAME}',
            '{LIST}',
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->initNamespace(),
            $this->getComment(),
            $this->getClassName(),
            $this->getList(),
        ];
    }

    /**
     * 初始化命名空间
     * @return string
     */
    protected function initNamespace(): string
    {
        return $this->getNamespace() . "\\Dto";
    }

    /**
     * 获取控制器注释
     * @return string
     */
    protected function getComment(): string
    {
        return $this->model->menu_name. 'Dto （导入导出）';
    }

    /**
     * 获取类名称
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName().'Dto';
    }

    /**
     * @return string
     */
    protected function getList(): string
    {
        $phpCode = '';
        foreach ($this->columns as $index => $column) {
            $phpCode .= str_replace(
                ['NAME', 'INDEX', 'FIELD'],
                [$column['column_comment'] ?: $column['column_name'], $index, $column['column_name']],
                $this->getCodeTemplate()
            );
        }
        return $phpCode;
    }

    protected function getCodeTemplate(): string
    {
        return sprintf(
            "    %s\n    %s\n\n",
            '#[ExcelProperty(value: "NAME", index: INDEX)]',
            'public string $FIELD;'
        );
    }

    /**
     * 获取业务名称
     * @return string
     */
    public function getBusinessName(): string
    {
        return Str::studly(str_replace(env('DB_PREFIX'), '', $this->model->table_name));
    }

    /**
     * 设置代码内容
     * @param string $content
     */
    public function setCodeContent(string $content)
    {
        $this->codeContent = $content;
    }

    /**
     * 获取代码内容
     * @return string
     */
    public function getCodeContent(): string
    {
        return $this->codeContent;
    }
}