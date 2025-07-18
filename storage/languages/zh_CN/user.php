<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
return [
    'id' => '用户ID，主键',
    'username' => '用户名',
    'user_type' => '用户类型：(100系统用户)',
    'nickname' => '用户昵称',
    'phone' => '手机',
    'email' => '用户邮箱',
    'avatar' => '用户头像',
    'signed' => '个人签名',
    'dashboard' => '后台首页类型',
    'status' => '状态 (1正常 2停用)',
    'login_ip' => '最后登陆IP',
    'login_time' => '最后登陆时间',
    'backend_setting' => '后台设置数据',
    'created_by' => '创建者',
    'updated_by' => '更新者',
    'created_at' => '创建时间',
    'updated_at' => '更新时间',
    'remark' => '备注',
    'username_exist' => '用户名已存在',
    'enums' => [
        'type' => [
            100 => '系统用户',
            200 => '普通用户',
        ],
        'status' => [
            1 => '正常',
            2 => '停用',
        ],
    ],
    'disable' => '账号已停用',
    'password' => '密码',
    'old_password_error' => '旧密码错误',
    'old_password' => '旧密码',
    'password_confirmation' => '确认密码',
    'department' => '部门',
];
