<?php
/**
* 
*/
class Logic
{
    public function logic($name)
    {
        $classname = $name.'Logic';
        require_once APP_ROOT."logic/$classname.php";
        return new $classname;
    }

    public function model($name)
    {
        $classname = $name.'Model';
        require_once APP_ROOT."model/$classname.php";
        return new $classname;
    }
}
