<?php

ini_set('display_errors' , true);
error_reporting(E_ALL | E_STRICT);

define('APP_ROOT', __DIR__.'/../');
define('TEST_ROOT', __DIR__.'/');

require_once '../vendor/testify/testify.class.php';

if ($f = file_exists(_name().'.php')) {
    include $f;
} else {
    die('no file '.$f);
}

function _name()
{
    $req_uri = reset(explode('?', $_SERVER['REQUEST_URI']));
    return trim($req_uri, '/');
}
