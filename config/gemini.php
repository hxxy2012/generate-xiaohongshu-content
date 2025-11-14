<?php
// +----------------------------------------------------------------------
// | Gemini API 配置
// +----------------------------------------------------------------------

return [
    // Gemini API Key（请在.env中配置）
    'api_key' => env('gemini.api_key', ''),

    // Gemini API 地址
    'api_url' => env('gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta'),

    // 使用的模型
    'model' => env('gemini.model', 'gemini-1.5-flash'),

    // 请求超时时间（秒）
    'timeout' => env('gemini.timeout', 60),

    // 最大重试次数
    'max_retries' => env('gemini.max_retries', 3),

    // 温度参数（0-2，越高越随机）
    'temperature' => env('gemini.temperature', 0.9),

    // Top P 参数
    'top_p' => env('gemini.top_p', 1),

    // Top K 参数
    'top_k' => env('gemini.top_k', 40),

    // 最大输出token数
    'max_output_tokens' => env('gemini.max_output_tokens', 2048),
];
