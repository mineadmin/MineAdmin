<?php
namespace App\System\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 老板管理Dto （导入导出）
 */
#[ExcelData]
class SystemBossDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "老板名称", index: 1)]
    public string $name;

    #[ExcelProperty(value: "老板代码", index: 2)]
    public string $code;

    #[ExcelProperty(value: "排序", index: 3)]
    public string $sort;

    #[ExcelProperty(value: "状态 (0正常 1停用)", index: 4)]
    public string $status;

    #[ExcelProperty(value: "创建者", index: 5)]
    public string $created_by;

    #[ExcelProperty(value: "更新者", index: 6)]
    public string $updated_by;

    #[ExcelProperty(value: "创建时间", index: 7)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 8)]
    public string $updated_at;

    #[ExcelProperty(value: "删除时间", index: 9)]
    public string $deleted_at;

    #[ExcelProperty(value: "备注", index: 10)]
    public string $remark;


}