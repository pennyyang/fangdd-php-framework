<?php

/**
* Model
* 对应一张表
*/
class Model
{
    public static $table;
    public static $pkey;
    private $_rules = array();

    public function table()
    {
        return static::$table;
    }

    public function t()
    {
        return self::table();
    }

    public function pkey()
    {
        return static::$pkey;
    }
    
    public function get($id)
    {
        return ORM::forTable(static::$table, static::$pkey)->find($id);
    }

    public function edit($id, $args)
    {
        $data = $this->filter($args);
        if (($valid = $this->valid($data)) !== true) {
            return reset(reset($valid));
        }
        return ORM::forTable(static::$table, static::$pkey)->where(static::$pkey, $id)->update($data);
    }

    public function add($args)
    {
        $data = $this->filter($args);
        if (($valid = $this->valid($data)) !== true) {
            return reset(reset($valid));
        }
        return ORM::forTable(static::$table, static::$pkey)->insert($data);
    }

    public function delete($id)
    {
        return ORM::forTable(static::$table, static::$pkey)->where(static::$pkey, $id)->delete();
    }

    public function __call($func, $args)
    {
        return call_user_func_array(array(ORM::forTable(static::$table, static::$pkey), $func), $args);
    }

    public function conditions($conditions)
    {
        $this->_conditions = $conditions;
        $orm = ORM::forTable(static::$table, static::$pkey);
        foreach ($conditions as $field => $value) {
            if (isset($this->_rules[$field])) {
                $rule = $this->_rules[$field];
                $orm->where($rule[0], isset($rule[1]) ? $rule[1] : '=', isset($rule[2]) ? $rule[2] :$value);
            } elseif (isset($this->fields[$field])) {
                $orm->where($field, $value);
            }
        }
        return $orm;
    }

    /**
     * rules(array(
     *     'gt_age' => array('birth_year', '>');
     * ));
     */
    public function rules($rules)
    {
        $this->_rules = $rules;
        return $this;
    }

    public function filter($data)
    {
        if (!isset(static::$fields)) {
            return $data;
        }
        $ret = array();
        foreach (static::$fields as $field => $value) {
            if (isset($data[$field])) {
                $ret[$field] = $data[$field];
            }
        }
        return $ret;
    }

    public function valid($data)
    {
        return true;
    }
}
