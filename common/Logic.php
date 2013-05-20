<?php
/**
* 逻辑基类
*/
class Logic
{
    protected function logic($name)
    {
        $classname = $name.'Logic';
        require_once APP_ROOT."logic/$classname.php";
        return new $classname;
    }

    protected function model($name)
    {
        $classname = $name.'Model';
        require_once APP_ROOT."model/$classname.php";
        return new $classname;
    }
}
