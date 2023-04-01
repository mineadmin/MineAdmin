<?php
namespace App\Setting\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 数据源管理Dto （导入导出）
 */
#[ExcelData]
class SettingDatasourceDto implements MineModelExcel
{
    #[ExcelProperty(value: "主键", index: 0)]
    public string $id;

    #[ExcelProperty(value: "数据源名称", index: 1)]
    public string $name;

    #[ExcelProperty(value: "数据库地址", index: 2)]
    public string $db_host;

    #[ExcelProperty(value: "数据库端口", index: 3)]
    public string $db_port;

    #[ExcelProperty(value: "数据库名称", index: 4)]
    public string $db_name;

    #[ExcelProperty(value: "数据库用户", index: 5)]
    public string $db_user;

    #[ExcelProperty(value: "数据库密码", index: 6)]
    public string $db_pass;

    #[ExcelProperty(value: "数据库字符集", index: 7)]
    public string $db_charset;

    #[ExcelProperty(value: "数据库字符序", index: 8)]
    public string $db_collation;

    #[ExcelProperty(value: "创建者", index: 9)]
    public string $created_by;

    #[ExcelProperty(value: "更新者", index: 10)]
    public string $updated_by;

    #[ExcelProperty(value: "创建时间", index: 11)]
    public string $created_at;

    #[ExcelProperty(value: "更新时间", index: 12)]
    public string $updated_at;

    #[ExcelProperty(value: "备注", index: 13)]
    public string $remark;


}