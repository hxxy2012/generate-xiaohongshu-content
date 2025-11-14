<?php
// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'redis'),

    // 缓存连接方式配置
    'stores' => [
        'file' => [
            // 驱动方式
            'type' => 'File',
            // 缓存保存目录
            'path' => runtime_path() . 'cache/',
            // 缓存前缀
            'prefix' => '',
            // 缓存有效期 0表示永久缓存
            'expire' => 0,
        ],
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            // 服务器地址
            'host' => env('redis.host', '127.0.0.1'),
            // 端口
            'port' => env('redis.port', 6379),
            // 密码
            'password' => env('redis.password', ''),
            // 缓存前缀
            'prefix' => 'redbook:',
            // 缓存有效期 0表示永久缓存
            'expire' => 0,
            // 选择数据库
            'select' => env('redis.select', 0),
            // 超时时间
            'timeout' => 0,
            // 是否持久化
            'persistent' => false,
        ],
    ],
];
