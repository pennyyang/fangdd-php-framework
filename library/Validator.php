<?php

/**
 * 验证类
 * @author Kangwang Xie <kingwon680@gmail.com>
 */
class Validator {

    static $fields = null;
    static $msg = null;

    public function getMsg() {
        return self::$msg;
    }

    public function setMsg($msg) {
        self::$msg = $msg;
    }

    /**
     * 按字段数据类型配置校验方法
     * @return type
     */
    public function validRules() {
        return array(
            'tinyint' => 'int',
            'smallint' => 'int',
            'mediumint' => 'int',
            'int' => 'int',
            'bigint' => 'int',
            'decimal' => 'number',
            'char' => 'isEmpty',
            'varchar' => 'isEmpty',
            'text' => 'isEmpty',
            'date' => 'date',
            'datetime' => 'datetime',
            'timestamp' => 'datetime',
//            'rules' => array('regex', 'between', 'in'),
        );
    }

    /**
     * 是否数字
     * @param type $value
     * @param type $msg
     * @return type
     */
    public function number($value, $msg) {
        return is_numeric($value) ? true : self::$msg[] = $msg . '不是数字';
    }

    /**
     * 是否整形
     * @param type $value
     * @param type $msg
     * @return type
     */
    public function int($value, $msg) {
        return (is_numeric($value) && inval($value) === $value) ? true : self::$msg[] = $msg . '不是整形';
    }

    public function alnum($value, $msg) {
        $rules = '/[a-zA-Z0-9]/';
        return preg_match($rules, $value) ? true : self::$msg[] = $msg . '不是字母或数字';
    }

    public function regex($value, $rules, $msg) {
        return preg_match($rules, $value) ? true : self::$msg[] = $msg . '不符合条件';
    }

    public function isEmpty($value, $msg) {
        return ($value != 0 && $value !== null) ? true : self::$msg[] = $msg . '不能为空';
    }

    public function between($value, $rules, $msg) {
        $msg .= implode('~', $rules);
        return ($value > $rules[0] && $value < $rules[1]) ? true : self::$msg[] = $msg . '不在' . $rules[0] . '-' . $rules[1];
    }

    public function in($value, $rules, $msg) {
        return in_array($value, $rules) ? true : self::$msg[] = $msg . '不在范围内';
    }

    public function email($value, $msg) {
        $rules = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        return preg_match($rules, $value) ? true : self::$msg[] = $msg . '不符合规则';
    }

    public function mobile($value, $msg) {
        $rules = "/^1([358]\d|4[57])\d{8}$/";
        return preg_match($rules, $value) ? true : self::$msg[] = $msg . '不符合规则';
    }

    public function string($value, $msg) {
        return is_string($value) ? true : self::$msg[] = $msg . '不是字符串';
    }

    public function datetime($value, $msg) {
        $rules = "/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/";
        return preg_match($rules, $value) ? true : self::$msg[] = $msg . '时间类型错误';
    }

    public function date($value, $msg) {
        $rules = "/[\d]{4}-[\d]{1,2}-[\d]{1,2}/";
        return preg_match($rules, $value) ? true : self::$msg[] = $msg . '时间类型错误';
    }

}

