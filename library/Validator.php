<?php

/**
 * 验证类
 * @author Kangwang Xie <kingwon680@gmail.com>
 */
class validator {

    static $fields = null;
    static $msg = null;

    public function getMsg() {
        return self::$msg;
    }

    public function setMsg($msg) {
        self::$msg[] = $msg;
    }

    /**
     * 是否数字
     * @param type $value
     * @param type $msg
     * @return type
     */
    public function number($value, $msg) {
        return is_numeric($value) ? true : self::$msg[] = $msg;
    }

    /**
     * 是否整形
     * @param type $value
     * @param type $msg
     * @return type
     */
    public function int($value, $msg) {
        return (is_numeric($value) && inval($value) === $value) ? true : self::$msg[] = $msg;
    }

    public function alnum($value, $msg) {
        $rules = '/[a-zA-Z0-9]/';
        return preg_match($rules, $value) ? true : self::$msg[] = $msg;
    }

    public function regex($value, $msg, $rules) {
        return preg_match($rules, $value) ? true : self::$msg[] = $msg;
    }

    public function isEmpty($value, $msg) {
        return ($value != 0 && $value == null) ? true : self::$msg[] = $msg;
    }

    public function between($value, $rules, $msg) {
        $msg .= implode('~', $rules);
        return ($value > $rules[0] && $value < $rules[1]) ? true : self::$msg[] = $msg;
    }

    public function in($value, $rules, $msg) {
        return in_array($value, $rules) ? true : self::$msg[] = $msg;
    }
    
    public function email($value, $msg){
        $rules = '';
    }

}

?>