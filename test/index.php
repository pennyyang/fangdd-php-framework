<?php

ini_set('display_errors' , true);
error_reporting(E_ALL | E_STRICT);

define('APP_ROOT', __DIR__.'/../');
define('TEST_ROOT', __DIR__.'/');

spl_autoload_register(function ($class_name) {
    $fpath = APP_ROOT.'library/'.$class_name.'.php';
    if (file_exists($fpath)) {
        require_once $fpath;
    }
});

require_once '../vendor/testify/testify.class.php';

$f = _name().'.php';
if (file_exists($f)) {
    include $f;
} else {
    die('no file '.$f);
}

function _name()
{
    $arr = parse_url($_SERVER['REQUEST_URI']);
    $req_uri = $arr['path'];
    return trim($req_uri, '/');
}
