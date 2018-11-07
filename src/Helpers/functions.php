<?php

/**
 * 助手函数
 * @author 刘健 <coder.liu@qq.com>
 */

if (!function_exists('app')) {
    // 返回当前App实例
    function app()
    {
        return \Mix::$app;
    }
}

if (!function_exists('env')) {
    // 获取一个环境变量的值
    function env($name = null)
    {
        return \Mix\Core\Env::get($name);
    }
}

if (!function_exists('tgo')) {
    // 创建一个带异常捕获的协程
    function tgo($closure)
    {
        \Mix\Core\Coroutine::create($closure);
    }
}
