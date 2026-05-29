<?php

namespace App\Http\Common;

/**
 * 全局响应码
 */
enum ResultCode: int
{
    /** 请求成功 */
    case Success = 200;

    /** 请求失败 */
    case Failure = 500;

    /** 未授权 */
    case Unauthorized = 401;

    /** 无权限 */
    case Forbidden = 403;

    /** 页面不存在 */
    case NotFound = 404;

    /** 请求参数错误 */
    case Unprocessable = 422;

    /** 已禁用 */
    case Disabled = 423;
}
