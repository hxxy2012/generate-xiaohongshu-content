<?php
declare(strict_types=1);

namespace app\index\validate;

use think\Validate;

/**
 * 注册验证器
 */
class RegisterValidate extends Validate
{
    protected $rule = [
        'username'  => 'require|alphaDash|length:3,20|unique:user',
        'password'  => 'require|length:6,20',
        'password_confirm' => 'require|confirm:password',
        'phone'     => 'mobile',
        'email'     => 'email',
        'nickname'  => 'length:2,20',
    ];

    protected $message = [
        'username.require'  => '用户名不能为空',
        'username.alphaDash'=> '用户名只能包含字母、数字、下划线和破折号',
        'username.length'   => '用户名长度必须在3-20个字符之间',
        'username.unique'   => '用户名已存在',
        'password.require'  => '密码不能为空',
        'password.length'   => '密码长度必须在6-20个字符之间',
        'password_confirm.require' => '确认密码不能为空',
        'password_confirm.confirm' => '两次密码输入不一致',
        'phone.mobile'      => '手机号格式不正确',
        'email.email'       => '邮箱格式不正确',
        'nickname.length'   => '昵称长度必须在2-20个字符之间',
    ];

    protected $scene = [
        'register' => ['username', 'password', 'password_confirm'],
    ];
}
