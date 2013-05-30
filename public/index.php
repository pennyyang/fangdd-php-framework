<?php
/**
 * 框架的入口文件
 * 定义常量，设置处理错误的方式
 * 
 * @author wangxiaochi
 */

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

// 定义常量 开发环境/生产环境
define('ENV', 'dev'); // may be 'prd'
define('APP_ROOT', __DIR__.'/../');
define('PUB_ROOT', __DIR__.'/');

// if in prd, mute all error reportings
if (ENV == 'prd') {
    ini_set('display_errors', 0);
}
// 总是将错误存到文件中
set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
    $dir = APP_ROOT.'log';
    if (!file_exists($dir)) {
        mkdir($dir);
    }
    $log_file = $dir.'/'.date('Ymd');
    $msg = '['.date('H:i:s').'] '.'PHP error '.$errno."\n"
        .$errstr."\n"
        .'file: '.$errfile.', line: '.$errline."\n";
    error_log($msg, 3, $log_file);
    return ENV == 'prd';
});
include APP_ROOT.'common/lead.php';
