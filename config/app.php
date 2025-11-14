<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用名称
    'app_name' => 'RedBookAI',

    // 应用调试模式
    'app_debug' => true,

    // 应用Trace
    'app_trace' => false,

    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用命名空间
    'app_namespace' => 'app',

    // 默认应用
    'default_app' => 'index',

    // 默认控制器名
    'default_controller' => 'Index',

    // 默认操作名
    'default_action' => 'index',

    // 异常页面的模板文件
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',

    // 显示错误信息
    'show_error_msg' => false,
];
