<?php
/**
* 
*/
class Logic
{
    public function logic($name)
    {
        $classname = $name.'Logic';
        return new $classname;
    }

    public function model($name)
    {
        $classname = $name.'Model';
        return new $classname;
    }
}
