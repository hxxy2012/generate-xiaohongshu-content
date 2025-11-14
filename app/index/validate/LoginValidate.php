<?php
declare(strict_types=1);

namespace app\index\validate;

use think\Validate;

/**
 * 登录验证器
 */
class LoginValidate extends Validate
{
    protected $rule = [
        'account'  => 'require',
        'password' => 'require',
    ];

    protected $message = [
        'account.require'  => '账号不能为空',
        'password.require' => '密码不能为空',
    ];
}
