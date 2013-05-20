<?php
/**
* 
*/
class Config
{
    private static $data;
    public static function init($root)
    {
        self::$_data = array_merge(
            include $root.'app.config.php',
            include $root.ENV.'.config.php'
        );
    }

    public static function get($key)
    {
        return isset(self::$_data[$key]) ? self::$_data[$key] : null;
    }
}
