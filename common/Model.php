<?php

/**
* Model
* 对应一张表
*/
class Model
{
    public static $table;
    public static $pkey;

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
        return ORM::forTable(static::$table, static::$pkey)->findOne($id);
    }

    public function edit($id, $args)
    {
        $data = $this->filter($args);
        $valid_data = $this->valid($data);
        return ORM::forTable(static::$table, static::$pkey)->where(static::$pkey, $id)->update($valid_data);
    }

    public function add($args)
    {
        $data = $this->filter($args);
        $valid_data = $this->valid($data);
        return ORM::forTable(static::$table, static::$pkey)->insert($valid_data);
    }

    public function delete($id)
    {
        return ORM::forTable(static::$table, static::$pkey)->where(static::$pkey, $id)->delete();
    }

    public function __call($func, $args)
    {
        return call_user_func_array(array(ORM::forTable(static::$table, static::$pkey), $func), $args);
    }
}
