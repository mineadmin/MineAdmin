<?php
/** @noinspection ThisExpressionReferencesGlobalObjectJS */
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
use Mine\Helper\Str;

/**
 * Vue index文件生成
 * Class VueIndexGenerator
 * @package Mine\Generator
 */
class VueIndexGenerator extends MineGenerator implements CodeGenerator
{
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
     * @return VueIndexGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): VueIndexGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)->orderByDesc('sort')
            ->get([
                'column_name', 'column_comment', 'is_query', 'is_pk', 'is_list', 'view_type', 'dict_type',
        ]);
        return $this;
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::lower($this->model->module_name);
        $path = BASE_PATH . "/runtime/generate/vue/src/views/{$module}/{$this->getShortBusinessName()}/index.vue";
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
        return $this->getStubDir().'/Vue/index.stub';
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
    protected function placeholderReplace(): VueIndexGenerator
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
            '{CODE}',
            '{HIDE_PAGE}',
            '{FIRST_SEARCH}',
            '{SEARCH_LIST}',
            '{COLUMN_LIST}',
            '{BUSINESS_EN_NAME}',
            '{QUERY_PARAMS}',
            '{DICT_LIST}',
            '{DICT_DATA}',
            '{PK}',
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->getCode(),
            $this->getHidePage(),
            $this->getFirstSearch(),
            $this->getSearchList(),
            $this->getColumnList(),
            $this->getBusinessEnName(),
            $this->getQueryParams(),
            $this->getDictList(),
            $this->getDictData(),
            $this->getPk(),
        ];
    }

    /**
     * 获取是否隐藏分页
     * @return string
     */
    protected function getHidePage(): string
    {
        return $this->model->type === 'single' ? 'false' : 'true';
    }

    /**
     * 获取标识代码
     * @return string
     */
    protected function getCode(): string
    {
        return Str::lower($this->model->module_name) . ':' . $this->getShortBusinessName();
    }

    /**
     * 获取第一个搜索
     * @return string
     */
    protected function getFirstSearch(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_query === '1') {
                $jsCode .= $this->getHtmlType($column);
                break;
            }
        }
        return $jsCode;
    }

    /**
     * 获取其余搜索列表
     * @return string
     */
    protected function getSearchList(): string
    {
        $jsCode = '';
        $k = 0;
        foreach ($this->columns as $column) {
            if ($column->is_query === '1') {
                if ($k > 0) {
                    $code = <<<js

            <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
                {$this->getHtmlType($column)}
            </el-form-item>
        
js;
                    $jsCode .= $code;

                } else {
                    $k = 1;
                }
            }
        }
        return $jsCode;
    }

    /**
     * 获取html类型
     * @param $column
     * @return string
     */
    protected function getHtmlType($column): string
    {
        if (!empty($column->dict_type)) {
            return <<<js
        
            <el-select v-model="queryParams.{$column->column_name}" style="width:100%" clearable placeholder="{$column->column_comment}">
                <el-option
                    v-for="(item, index) in {$column->dict_type}_data"
                    :key="index"
                    :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
js;
        }

        if ($column->view_type == 'date') {
            return <<<js
<el-date-picker
                    v-model="queryParams.{$column->column_name}"
                    type="date"
                    placeholder="选择{$column->column_comment}">
                </el-date-picker>
js;

        }

        return <<<js
<el-input v-model="queryParams.{$column->column_name}" placeholder="{$column->column_comment}" clearable></el-input>
js;
    }

    /**
     * 获取表格显示列
     * @return string
     * @noinspection CheckTagEmptyBody
     */
    protected function getColumnList(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_list === '1') {
                $code = <<<js
 
         <el-table-column
            label="{$column->column_comment}"
            prop="{$column->column_name}"
         />
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
     * 获取需要搜索的字段列表
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getQueryParams(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_query === '1') {
                $code = <<<js

           {$column->column_name}: undefined,
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