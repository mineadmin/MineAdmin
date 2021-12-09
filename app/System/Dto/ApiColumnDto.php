<?php
namespace App\System\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * @ExcelData
 */
class ApiColumnDto implements MineModelExcel
{
    /**
     * @ExcelProperty(value="所属接口ID", index="0")
     */
    public $api_id;

    /**
     * @ExcelProperty(value="接口名称", index="1")
     */
    public $name;

    /**
     * @ExcelProperty(value="类型 0 请求 1 响应", index="2")
     */
    public $type;

    /**
     * @ExcelProperty(value="数据类型", index="3")
     */
    public $data_type;
    
    /**
     * @ExcelProperty(value="是否必填 0 非必填 1 必填", index="4")
     */
    public $is_required;

    /**
     * @ExcelProperty(value="默认值", index="5")
     */
    public $default_value;

    /**
     * @ExcelProperty(value="字段说明", index="6")
     */
    public $description;
}