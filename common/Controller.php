<?php

/**
* 控制器的基类
*/
class Controller
{
    protected function logic($name)
    {
        $classname = $name.'Logic';
        require_once APP_ROOT."logic/$classname.php";
        return new $classname;
    }
}
