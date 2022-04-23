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

namespace Mine\Generator\Traits;

use App\Setting\Model\SettingGenerateColumns;
use Mine\Helper\Str;

trait VueSaveGeneratorTraits
{
    /**
     * 获取表单列表代码
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function getFormListCode(SettingGenerateColumns $column): string
    {
        return match ($column->view_type) {
            'password'  => $this->passwordCode($column),
            'textarea'  => $this->textareaCode($column),
            'select'    => $this->selectCode($column),
            'radio'     => $this->radioCode($column),
            'checkbox'  => $this->checkboxCode($column),
            'date'      => $this->dateCode($column),
            'image'     => $this->imageCode($column),
            'file'      => $this->fileCode($column),
            'editor'    => $this->editorCode($column),
            'selectResourceRadio' => $this->selectResourceRadio($column),
            'selectResourceMulti' => $this->selectResourceMulti($column),
            default     => $this->textCode($column),
        };
    }

    /**
     * text
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function textCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('text')
        );
    }

    /**
     * password
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function passwordCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('password')
        );
    }

    /**
     * textareaCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function textareaCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('textarea')
        );
    }

    /**
     * selectCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function selectCode(SettingGenerateColumns $column): string
    {
        $dictCode = '';
        if ($column->dict_type) {
            $dictCode = str_replace(
                '{DICT_COLUMN}', $column->dict_type, $this->getFormItemTemplate('selectOption')
            );
        }
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{SELECT_OPTION}'],
            [$column->column_name, $column->column_comment, $dictCode],
            $this->getFormItemTemplate('select')
        );
    }

    /**
     * radioCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function radioCode(SettingGenerateColumns $column): string
    {
        $dictCode = '';
        if ($column->dict_type) {
            $dictCode = str_replace(
                '{DICT_COLUMN}', $column->dict_type, $this->getFormItemTemplate('radioOption')
            );
        }
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{RADIO_OPTION}'],
            [$column->column_name, $column->column_comment, $dictCode],
            $this->getFormItemTemplate('radio')
        );
    }

    /**
     * checkboxCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function checkboxCode(SettingGenerateColumns $column): string
    {
        $dictCode = '';
        if ($column->dict_type) {
            $dictCode = str_replace(
                '{DICT_COLUMN}', $column->dict_type, $this->getFormItemTemplate('checkboxOption')
            );
        }
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{CHECKBOX_OPTION}'],
            [$column->column_name, $column->column_comment, $dictCode],
            $this->getFormItemTemplate('checkbox')
        );
    }

    /**
     * dateCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function dateCode(SettingGenerateColumns $column): string
    {
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-date-picker
                type="date"
                placeholder="请选择{$column->column_comment}"
                v-model="form.{$column->column_name}"
                style="width: 100%;"
            ></el-date-picker>
        </el-form-item>

VUE;
    }

    /**
     * 资源选择单选模式
     * selectResourceRadio
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function selectResourceRadio(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('selectResourceRadio')
        );
    }

    /**
     * 资源选择多选模式
     * selectResourceMulti
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function selectResourceMulti(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('selectResourceMulti')
        );
    }

    /**
     * imageCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function imageCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{COLUMN_TITLE_NAME}'],
            [$column->column_name, $column->column_comment, Str::studly($column->column_name)],
            $this->getFormItemTemplate('uploadImage')
        );
    }

    /**
     * fileCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function fileCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{COLUMN_TITLE_NAME}'],
            [$column->column_name, $column->column_comment, Str::studly($column->column_name)],
            $this->getFormItemTemplate('uploadFile')
        );
    }

    /**
     * editorCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function editorCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('editor')
        );
    }

    /**
     * @param string $tpl
     * @return string
     */
    protected function getFormItemTemplate(string $tpl): string
    {
        return $this->filesystem->sharedGet($this->getStubDir() . "/Vue/formItem/{$tpl}.stub");
    }

}