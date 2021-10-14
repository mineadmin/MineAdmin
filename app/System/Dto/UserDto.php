<?php
namespace App\System\Dto;

use Mine\Interfaces\MineModelExcel;
use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;

/**
 * @ExcelData
 */
class UserDto implements MineModelExcel
{
    /**
     * @ExcelProperty(value="用户名", index="0")
     */
    public $username;

    /**
     * @ExcelProperty(value="密码", index="3")
     */
    public $password;

    /**
     * @ExcelProperty(value="昵称", index="1")
     */
    public $nickname;
    
    /**
     * @ExcelProperty(value="手机", index="2")
     */
    public $phone;
}