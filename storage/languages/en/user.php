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
    'Id ' => ' User ID, primary key ',
    'Username ' => ' Username ',
    'user_type ' => ' User type: (100 system users) ',
    'Nickname ' => ' user nickname ',
    'Phone ' => ' Phone ',
    'Email ' => ' User Email ',
    'Avatar ' => ' user avatar ',
    'Signed ' => ' Personal signature ',
    'Dashboard ' => ' Backstage Home Type ',
    'Status' => 'Status (1 normal 2 deactivated) ',
    'login_ip ' => ' Last login IP ',
    'login_time ' => ' Last login time ',
    'backend_setting' => 'Background settings data',
    'created_by ' => ' Creator ',
    'updated_by ' => ' Updater ',
    'created_at' => 'Creation time',
    'updated_at ' => ' Update time ',
    'Remark ' => ' Remark ',
    'username_exist' => 'Username already exists',
    'Enums' => [
        'Type ' => [
            100 => 'System user',
            200 => 'Normal user',
        ],
        'Status' => [
            1 => 'Normal',
            2 => 'Deactivated',
        ],
    ],
    'disable' => 'Account deactivated',
    'old_password_error' => 'Old password error',
    'old_password ' => ' Old password ',
    'password_confirmation ' => ' Confirm password ',
    'password' => ' Password ',
];
