<?php

/**
* 
*/
class App
{
    private static $app;
    
    /**
     * 私有的构造函数，在单例模式中防止误初始化
     */
    private function __construct()
    {
        # code...
    }

    /**
     * 通过这个方法获得单例
     */
    public static function app()
    {
        return $app;
    }
}
