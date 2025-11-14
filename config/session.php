<?php
// +----------------------------------------------------------------------
// | Session设置
// +----------------------------------------------------------------------

return [
    // Session类型
    'type'           => env('session.type', 'file'),
    // Session有效期
    'expire'         => env('session.expire', 86400),
    // Session前缀
    'prefix'         => env('session.prefix', 'redbook_session_'),
    // 驱动方式
    'store'          => '',
    // Session命名空间
    'namespace'      => '\\think\\session\\driver\\',
    // 自动开启Session
    'auto_start'     => true,

    // 文件Session
    'file' => [
        'path'       => runtime_path() . 'session/',
        'expire'     => 86400,
    ],
];
