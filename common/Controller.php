<?php

/**
* 控制器的基类
*/
class Controller
{
    public function __construct()
    {
        $db_conf = Config::get('db');
        ORM::config($db_conf['dsn']);
        ORM::config('username', $db_conf['username']);
        ORM::config('password', $db_conf['password']);
    }

    protected function logic($name)
    {
        $classname = $name.'Logic';
        require_once APP_ROOT."logic/$classname.php";
        return new $classname;
    }
}
