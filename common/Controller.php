<?php

/**
* 
*/
class Controller
{
    public function logic($name)
    {
        $classname = $name.'Logic';
        require_once APP_ROOT."logic/$classname.php";
        return new $classname;
    }
}
