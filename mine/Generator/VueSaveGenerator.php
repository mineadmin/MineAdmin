<?php
/** @noinspection ThisExpressionReferencesGlobalObjectJS */
/** @noinspection JSValidateTypes */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
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
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Generator\Traits\VueSaveGeneratorTraits;
use Mine\Helper\Str;

/**
 * Vue index文件生成
 * Class VueSaveGenerator
 * @package Mine\Generator
 */
class VueSaveGenerator extends MineGenerator implements CodeGenerator
{
    use VueSaveGeneratorTraits;

    /**
     * @var SettingGenerateTables
     */
    protected $model;

    /**
     * @var string
     */
    protected $codeContent;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $columns;

    /**
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @return VueSaveGenerator
     */
    public function setGenInfo(SettingGenerateTables $model): VueSaveGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)->orderByDesc('sort')
            ->get([
                'column_name', 'column_comment', 'is_required',
                'is_pk', 'is_insert', 'is_edit', 'view_type', 'dict_type',
        ]);
        return $this;
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::lower($this->model->module_name);
        $path = BASE_PATH . "/runtime/generate/vue/src/views/{$module}/{$this->getShortBusinessName()}/save.vue";
        $this->filesystem->makeDirectory(
            BASE_PATH . "/runtime/generate/vue/src/views/{$module}/{$this->getShortBusinessName()}",
            0755, true, false
        );
        $this->filesystem->put($path, $this->placeholderReplace()->getCodeContent());
    }

    /**
     * 预览代码
     */
    public function preview(): string
    {
        return $this->placeholderReplace()->getCodeContent();
    }

    /**
     * 获取模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir().'/Vue/save.stub';
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
    protected function placeholderReplace(): VueSaveGenerator
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
            '{BUSINESS_EN_NAME}',
            '{TREE_REQUEST}',
            '{BUSINESS_NAME}',
            '{FORM_LIST}',
            '{FORM_DATA}',
            '{REQUIRED_LIST}',
            '{SET_FORM_DATA}',
            '{DICT_LIST}',
            '{DICT_DATA}',
            '{SELECT_RESOURCE}',
            '{UPLOAD_IMAGE}',
            '{UPLOAD_FILE}',
            '{PK}',
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->getBusinessEnName(),
            $this->getTreeRequest(),
            $this->getBusinessName(),
            $this->getFormList(),
            $this->getFormData(),
            $this->getRequiredList(),
            $this->getSetFormData(),
            $this->getDictList(),
            $this->getDictData(),
            $this->getSelectResource(),
            $this->getUploadImage(),
            $this->getUploadFile(),
            $this->getPk(),
        ];
    }

    protected function getTreeRequest(): string
    {
        if ($this->model->type === 'tree') {
            return <<<js
    this.\$API.{$this->getBusinessEnName()}.tree().then(res => {
                this.treeList = res.data
            })
js;
        } else {
            return '';
        }
    }

    /**
     * 获取其余搜索列表
     * @return string
     */
    protected function getFormList(): string
    {
        $jsCode = '';
        // 对树表生成级联选择器
        if (!empty($this->model->options['tree_parent_id'])) {
            $parent_id = $this->model->options['tree_parent_id'];
$jsCode .= <<<js

        <el-form-item label="父ID" prop="{$parent_id}">
            <el-cascader
                v-model="form.{$parent_id}"
                clearable
                style="width:100%"
                :options="treeList"
                :props="{ checkStrictly: true }"
            ></el-cascader>
        </el-form-item>

js;
        }

        foreach ($this->columns as $column) {
            if (isset($this->model->options['tree_parent_id']) && $this->model->options['tree_parent_id'] === $column->column_name) {
                continue;
            }
            if ($column->is_insert === '1' || $column->is_edit === '1') {
                $jsCode .= $this->getFormListCode($column);
            }
        }
        return $jsCode;
    }

    /**
     * 获取第一个搜索
     * @return string
     * @noinspection CommaExpressionJS
     */
    protected function getFormData(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_pk === '1' || $column->is_insert === '1' || $column->is_edit === '1') {
                $code = <<<js

            {$column->column_name}: '',
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取其余搜索列表
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getRequiredList(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_required === '1') {
                $code = <<<js

            {$column->column_name}: [{required: true, message: '{$column->column_comment}必填', trigger: 'blur' }],
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取表格显示列
     * @return string
     */
    protected function getSetFormData(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_pk === '1' || $column->is_insert === '1' || $column->is_edit === '1') {
                $code = <<<js

           this.form.{$column->column_name} = data.{$column->column_name};
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取字典数据
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getDictList(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if (!empty($column->dict_type)) {
                $code = <<<js

           this.getDict('{$column->dict_type}').then(res => {
               this.{$column->dict_type}_data = res.data
           })
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取字典变量
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getDictData(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if (!empty($column->dict_type)) {
                $code = <<<js
 
         {$column->dict_type}_data: [],
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取资源选择处理代码
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getSelectResource(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            $name = Str::studly($column->column_name);
            if ($column->view_type == 'selectResource') {
                $code = <<<js
 
        uploadSuccess{$name} (dataList) {
            this.form.{$column->column_name} = dataList
        },
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取图片上传处理代码
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getUploadImage(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            $name = Str::studly($column->column_name);
            if ($column->view_type == 'image') {
                $code = <<<js
 
        handlerUploadImage{$name} (res) {
            if (res.success) {
                this.form.{$column->column_name} = res.url
            }
        },
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取文件上传处理代码
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getUploadFile(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            $name = Str::studly($column->column_name);
            if ($column->view_type == 'file') {
                $code = <<<js
 
        handlerUploadFile{$name} (res) {
            if (res.success) {
                this.form.{$column->column_name} = res.url
            }
        },
 js;
                $jsCode .= $code;
            }
        }
        return $jsCode;
    }

    /**
     * 获取业务英文名
     * @return string
     */
    protected function getBusinessEnName(): string
    {
        return Str::camel(str_replace(env('DB_PREFIX'), '', $this->model->table_name));
    }

    /**
     * 获取业务名
     * @return string
     */
    protected function getBusinessName(): string
    {
        return str_replace('管理', '', $this->model->menu_name);
    }

    /**
     * 返回主键
     * @return string
     */
    protected function getPk(): string
    {
        foreach ($this->columns as $column) {
            if ($column->is_pk == '1') {
                return $column->column_name;
            }
        }
        return '';
    }

    /**
     * 获取短业务名称
     * @return string
     */
    public function getShortBusinessName(): string
    {
        return Str::camel(str_replace(
            Str::lower($this->model->module_name),
            '',
            str_replace(env('DB_PREFIX'), '', $this->model->table_name)
        ));
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