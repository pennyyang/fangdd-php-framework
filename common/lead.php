<?php

require_once APP_ROOT.'common/Model.php';
require_once APP_ROOT.'common/Logic.php';
require_once APP_ROOT.'common/Controller.php';

// auto load libary class
spl_autoload_register(function ($class_name) {
    $fpath = APP_ROOT.'library/'.$class_name.'.php';
    if (file_exists($fpath)) {
        require_once $fpath;
    }
});

ob_start();
session_start();

Config::init(APP_ROOT.'config/');

$router = new Router();
$router->dispath();
