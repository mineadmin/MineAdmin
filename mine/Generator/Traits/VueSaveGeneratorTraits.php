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
            'date'      => $this->dateCode($column),        // 1
            'time'      => $this->timeCode($column),        // 1
            'image'     => $this->imageCode($column),
            'file'      => $this->fileCode($column),
            'editor'    => $this->editorCode($column),
            'inputNumber' => $this->inputNumber($column),
            'switch'      => $this->switchCode($column),
            'rate'        => $this->rateCode($column),
            'slider'      => $this->sliderCode($column),
            'area'        => $this->areaCode($column),
            'colorPicker' => $this->colorPickerCode($column),
            'userSelect'  => $this->userSelectCode($column),
            'userinfo'    => $this->userinfoCode($column),
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
        if (!empty($column->options['select'])) {
            foreach ($column->options['select'] as $item) {
                $dictCode .= sprintf(
                    "  <el-option label=\"%s\" value=\"%s\" />\n        ",
                    $item['name'],
                    $item['value']
                );
            }
        } else if ($column->dict_type) {
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
        if (!empty($column->options['radio'])) {
            foreach ($column->options['radio'] as $item) {
                $dictCode .= sprintf(
                    "  <el-radio label=\"%s\">%s</el-radio>\n        ",
                    $item['value'],
                    $item['name']
                );
            }
        } else if ($column->dict_type) {
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
        if (!empty($column->options['checkbox'])) {
            foreach ($column->options['checkbox'] as $item) {
                $dictCode .= sprintf(
                    "  <el-checkbox label=\"%s\" />%s</el-checkbox>\n        ",
                    $item['value'],
                    $item['name']
                );
            }
        } else if ($column->dict_type) {
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
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{DATE_TYPE}', '{WEEK_FORMAT}', '{RANGE_TIPS}'],
            [
                $column->column_name,
                $column->column_comment,
                $column->options['date'],
                $column->options['date'] === 'week' ? 'format="第 ww 周"' : '',
                strpos($column->options['date'], 'range') > 0 ? 'start-placeholder="起始时间" end-placeholder="结束时间"' : ''
            ],
            $this->getFormItemTemplate('date')
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function timeCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('time')
        );
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
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function inputNumber(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('inputNumber')
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function switchCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('switch')
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function rateCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('rate')
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function sliderCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('slider')
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function areaCode(SettingGenerateColumns $column): string
    {
        $type = $column->options['area']['type'] === 'select' ? 'areaSelect' : 'areaCascader';
        $value = $column->options['area']['type'] === 'code' ? 'code' : 'name';

        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}', '{AREA_VALUE_TYPE}'],
            [$column->column_name, $column->column_comment, $value],
            $this->getFormItemTemplate($type)
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function colorPickerCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('colorPicker')
        );
    }

    /**
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function userSelectCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('userSelect')
        );
    }

    /**
     * 用户信息
     * @param SettingGenerateColumns $column
     * @return string
     */
    protected function userinfoCode(SettingGenerateColumns $column): string
    {
        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getFormItemTemplate('userinfo')
        );
    }
}