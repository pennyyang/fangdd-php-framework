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
        if ($classname::$table === null) {
            $classname::$table = preg_replace_callback('/[A-Z]/', function ($m) { return strtolower('_'.$m); }, lcfirst($name));
        }
        if ($classname::$pkey === null) {
            $classname::$pkey = $classname::$table.'_id';
        }
        return new $classname;
    }
}
