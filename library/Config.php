<?php
/**
* 配置
* 只读
*/
class Config
{
    private static $_data;

    /**
     * 初始化配置
     * @param string $root 配置文件根目录
     */
    public static function init($root) {
        self::$_data = array_merge(
            include $root.'app.config.php',
            include $root.ENV.'.config.php'
        );
    }

    /**
     * 获取配置
     * @param string $key
     * @return array 配置
     */
    public static function get($key = null) {
        if ($key === null)
            return self::$_data;
        return isset(self::$_data[$key]) ? self::$_data[$key] : null;
    }
}
