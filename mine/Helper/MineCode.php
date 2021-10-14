<?php /** @noinspection PhpUnused */


namespace Mine\Helper;


class MineCode
{
    public const TOKEN_EXPIRED = 1001;      // TOKEN过期、不存在
    public const VALIDATE_FAILED = 1002;    // 数据验证失败
    public const NO_PERMISSION = 1003;      // 没有权限
    public const NO_DATA = 1004;            // 没有数据
    public const NORMAL_STATUS = 1005;      // 正常状态异常代码

    public const NO_USER = 1010;            // 用户不存在
    public const PASSWORD_ERROR = 1011;     // 密码错误
    public const USER_BAN = 1012;           // 用户被禁

    public const METHOD_NOT_ALLOW = 2000;   // 地址使用了不允许的访问方法
}