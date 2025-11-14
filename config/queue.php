<?php
// +----------------------------------------------------------------------
// | 队列设置
// +----------------------------------------------------------------------

return [
    // 默认队列连接
    'default' => env('queue.driver', 'redis'),

    // 队列连接配置
    'connections' => [
        'sync' => [
            'type' => 'sync',
        ],
        'redis' => [
            'type' => 'redis',
            'host' => env('redis.host', '127.0.0.1'),
            'port' => env('redis.port', 6379),
            'password' => env('redis.password', ''),
            'select' => env('redis.queue_select', 1),
            'timeout' => 0,
            'persistent' => false,
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => null,
        ],
    ],

    // 任务失败记录
    'failed' => [
        'type' => 'none',
    ],
];
