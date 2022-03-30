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
            'tabs'    => $this->tabsCode($column),
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
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-input v-model="form.{$column->column_name}" clearable placeholder="请输入{$column->column_comment}" />
        </el-form-item>

VUE;
    }

    /**
     * password
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function passwordCode(SettingGenerateColumns $column): string
    {
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-input v-model="form.{$column->column_name}" show-password clearable placeholder="请输入{$column->column_comment}" />
        </el-form-item>

VUE;
    }

    /**
     * textareaCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function textareaCode(SettingGenerateColumns $column): string
    {
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-input v-model="form.{$column->column_name}" type="textarea" :rows="3" clearable placeholder="请输入{$column->column_comment}" />
        </el-form-item>

VUE;
    }

    /**
     * selectCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function selectCode(SettingGenerateColumns $column): string
    {
        if ($column->dict_type) {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-select v-model="form.{$column->column_name}" style="width:100%" clearable placeholder="请选择{$column->column_comment}">
                <el-option
                    v-for="(item, index) in {$column->dict_type}_data"
                    :key="index" :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
        </el-form-item>

VUE;
        } else {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-select v-model="form.{$column->column_name}" style="width:100%" clearable placeholder="请选择{$column->column_comment}">
            </el-select>
        </el-form-item>

VUE;
        }
    }

    /**
     * radioCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function radioCode(SettingGenerateColumns $column): string
    {
        if ($column->dict_type) {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-radio-group v-model="form.{$column->column_name}">
                <el-radio
                    v-for="(item, index) in {$column->dict_type}_data"
                    :key="index"
                    :label="item.value"
                >{{item.label}}</el-radio>
            </el-radio-group>
        </el-form-item>

VUE;

        } else {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-radio-group v-model="form.{$column->column_name}">
                <el-radio label="0">是</el-radio>
                <el-radio label="1">否</el-radio>
            </el-radio-group>
        </el-form-item>

VUE;
        }
    }

    /**
     * checkboxCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function checkboxCode(SettingGenerateColumns $column): string
    {
        if ($column->dict_type) {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-checkbox-group v-model="form.{$column->column_name}">
                <el-checkbox
                    v-for="(item, index) in {$column->dict_type}_data"
                    :key="index"
                    :label="item.value"
                >{{item.label}}</el-checkbox>
            </el-checkbox-group>
        </el-form-item>

VUE;
        } else {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-checkbox-group v-model="form.{$column->column_name}">
            </el-checkbox-group>
        </el-form-item>

VUE;
        }
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
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <sc-upload v-model="form.{$column->column_name}" title="选择{$column->column_comment}" file-select />
        </el-form-item>

VUE;
    }

    /**
     * 资源选择多选模式
     * selectResourceMulti
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function selectResourceMulti(SettingGenerateColumns $column): string
    {
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <sc-upload-multiple v-model="form.{$column->column_name}" title="选择{$column->column_comment}" file-select />
        </el-form-item>

VUE;
    }

    /**
     * imageCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function imageCode(SettingGenerateColumns $column): string
    {
        $name = Str::studly($column->column_name);
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <sc-upload
                v-model="form.{$column->column_name}"
                title="上传{$column->column_name}"
                :compress="1"
                :aspectRatio="1/1"
                @success="handlerUploadImage{$name}"
            />
        </el-form-item>

VUE;
    }

    /**
     * fileCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function fileCode(SettingGenerateColumns $column): string
    {
        $name = Str::studly($column->column_name);
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <sc-upload
                v-model="form.{$column->column_name}"
                title="上传{$column->column_name}"
                type="file"
                :compress="1"
                :aspectRatio="1/1"
                @success="handlerUploadFile{$name}"
            />
        </el-form-item>

VUE;
    }

    /**
     * editorCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function editorCode(SettingGenerateColumns $column): string
    {
        return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <editor v-model="form.{$column->column_name}" placeholder="请输入{$column->column_comment}" :height="400"></editor>
        </el-form-item>

VUE;
    }

    /**
     * tabsCode
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function tabsCode(SettingGenerateColumns $column): string
    {
        if ($column->dict_type) {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-select v-model="form.{$column->column_name}" style="width:100%" clearable placeholder="请选择{$column->column_comment}">
                <el-option
                    v-for="(item, index) in {$column->dict_type}_data"
                    :key="index" :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
        </el-form-item>

VUE;
        } else {
            return <<<VUE

        <el-form-item label="{$column->column_comment}" prop="{$column->column_name}">
            <el-select v-model="form.{$column->column_name}" style="width:100%" clearable placeholder="请选择{$column->column_comment}">
            </el-select>
        </el-form-item>

VUE;
        }
    }

}