<?php
/**
 * 引导文件
 * @author wangxiaochi <cumt.xiaochi@gmail.com>
 */

// 引入模型、逻辑、控制器和视图的基类
require_once APP_ROOT.'common/Model.php';
require_once APP_ROOT.'common/Logic.php';
require_once APP_ROOT.'common/Controller.php';
require_once APP_ROOT.'common/View.php';

// 自动载入类库
spl_autoload_register(function ($class_name) {
    $fpath = APP_ROOT.'library/'.$class_name.'.php';
    if (file_exists($fpath)) {
        require $fpath;
    }
});

ob_start();
session_start();

Config::init(APP_ROOT.'config/');

// 路由
$arr = parse_url($_SERVER['REQUEST_URI']);
$router = new Router($arr['path']);
$view = $router->dispath();
if ($view) {
    $view->layout('master');
    $view->render();
}
