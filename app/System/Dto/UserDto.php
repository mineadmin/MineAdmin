<?php
namespace App\System\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * 用户DTO
 */
#[ExcelData]
class UserDto implements MineModelExcel
{
    #[ExcelProperty(value: "用户名", index: 0)]
    public string $username;

    #[ExcelProperty(value: "密码", index: 3)]
    public string $password;

    #[ExcelProperty(value: "昵称", index: 1)]
    public string $nickname;

    #[ExcelProperty(value: "手机", index: 2)]
    public string $phone;

    #[ExcelProperty(value: "状态", index: 4, dictName: "data_status")]
    public string $status;
}