<?php

/**
* Sql Expression
*/
class Expression
{
    private $_sql;
    private $_values;
    
    public function __construct($sql, array $values = array())
    {
        $this->_sql = $sql;
        $this->_values = $values;
    }

    public function sql()
    {
        return $this->_sql;
    }

    public function values()
    {
        return $this->_values;
    }
}

/**
* ORM, Table
*/
class Model
{
    public static $_db;

    private static $_logs = array();
    private static $_last_sql;
    private static $_rawLogs;

    // 默认配置
    private static $_config = array(
        'driver_options' => array(),
        'logging' => false,
        'debug' => false,
    );

    private $_primaryKey;
    private $_raw = false;
    private $_count = false;
    private $_selects = array();
    private $_table;
    private $_as;
    private $_tables = array();
    private $_wheres = array();
    private $_havings = array();
    private $_groupbys = array();
    private $_orderbys = array();
    private $_distinct = false;
    private $_limit = 1000;
    private $_offset = 0;

    public static function config()
    {
        $argNum = func_num_args();
        if ($argNum == 1) {
            $a = func_get_arg(0);
            if (is_string($a)) {
                self::$_config['dsn'] = $a;
            } elseif (is_array($a)) {
                foreach ($a as $key => $value) {
                    self::$_config[$key] = $value;
                }
            }
        } else {
            $key   = func_get_arg(0);
            $value = func_get_arg(1);
            self::$_config[$key] = $value;
        }
    }

    public static function db() {
        if (!self::$_db) {
            self::$_db = new PDO(
                self::$_config['dsn'], 
                self::$_config['username'], 
                self::$_config['password'],
                self::$_config['driver_options']
            );
        }
        return self::$_db;
    }

    public static function forTable($table, $pkey = null)
    {
        $orm = new self();
        $orm->_table = self::table();
        $orm->_primaryKey = $pkey ?: self::pkey();
        return $orm;
    }

    public function where($key, $value)
    {
        return $this->whereEqual($key, $value);
    }

    public function whereEqual($key, $value)
    {
        return $this->_whereRaw($key, '=?', array($value));
    }

    public function whereNotEqual($key, $value)
    {
        return $this->_whereRaw($key, ' <> ?', array($value));
    }

    public function whereLike($key, $value)
    {
        return $this->_whereRaw($key, ' LIKE ?', array($value));
    }

    public function whereNotLike($key, $value)
    {
        return $this->_whereRaw($key, ' NOT LIKE ?', array($value));
    }

    public function whereLt($key, $value)
    {
        return $this->_whereRaw($key, ' < ?', array($value));
    }

    public function whereLte($key, $value)
    {
        return $this->_whereRaw($key, ' <= ?', array($value));
    }

    public function whereGt($key, $value)
    {
        return $this->_whereRaw($key, ' > ?', array($value));
    }

    public function whereGte($key, $value)
    {
        return $this->_whereRaw($key, ' >= ?', array($value));
    }

    public function whereId($id)
    {
        return $this->_whereRaw($this->_primaryKey, '=?', array($id));
    }

    public function whereIn($key, $values)
    {
        return $this->_whereIn($key, $values, true);
    }

    public function whereNotIn($key, $values)
    {
        return $this->_whereIn($key, $values, false);
    }

    private function _whereIn($key, $values, $is)
    {
        $ps = array_map(function ($v) { return '?'; }, $values);
        $str = ($is ? ' ' : ' NOT ').'IN ('.implode(',', $qs).')';
        return $this->_whereRaw($key, $str, $values);
    }

    private function _whereRaw($key, $midExpr, $values)
    {
        $key = self::_backQuote($key);
        return $this->whereRaw($key.$midExpr, $values);
    }

    public static function _backQuote($key)
    {
        if (strpos($key, '.')) {
            $arr = explode('.', $key);
            return self::_backQuoteWord($arr[0]).'.'.self::_backQuoteWord($arr[1]);
        } else {
            return self::_backQuoteWord($key);
        }
    }

    public static function _backQuoteWord($key)
    {
        if (strpos($key, '`') === false) {
            return "`$key`";
        }
        return $key;
    }

    public function whereRaw($str, $values)
    {
        $this->_wheres[] = array($str => $values);
        return $this;
    }

    public function having($key, $value)
    {
        return $this->havingEqual($key, $value);
    }

    public function havingEqual($key, $value)
    {
        return $this->_havingRaw($key, '=?', array($value));
    }

    public function havingNotEqual($key, $value)
    {
        return $this->_havingRaw($key, ' <> ?', array($value));
    }

    public function havingLike($key, $value)
    {
        return $this->_havingRaw($key, ' LIKE ?', array($value));
    }

    public function havingNotLike($key, $value)
    {
        return $this->_havingRaw($key, ' NOT LIKE ?', array($value));
    }

    public function havingLt($key, $value)
    {
        return $this->_havingRaw($key, ' < ?', array($value));
    }

    public function havingLte($key, $value)
    {
        return $this->_havingRaw($key, ' <= ?', array($value));
    }

    public function havingGt($key, $value)
    {
        return $this->_havingRaw($key, ' > ?', array($value));
    }

    public function havingGte($key, $value)
    {
        return $this->_havingRaw($key, ' >= ?', array($value));
    }

    public function havingId($id) // do we need this function?
    {
        return $this->_havingRaw($this->_primaryKey, '=?', array($id));
    }

    public function havingIn($key, $values)
    {
        return $this->_havingIn($key, $values, true);
    }

    public function havingNotIn($key, $values)
    {
        return $this->_havingIn($key, $values, false);
    }

    private function _havingIn($key, $values, $is)
    {
        $ps = array_map(function ($v) { return '?'; }, $values);
        $str = ($is ? ' ' : ' NOT ').'IN ('.implode(',', $qs).')';
        return $this->_havingRaw($key, $str, $values);
    }

    private function _havingRaw($key, $midExpr, $values)
    {
        $key = self::_backQuote($key);
        return $this->havingRaw($key.$midExpr, $values);
    }

    public function havingRaw($str, $values)
    {
        $this->_havings[] = array($str => $values);
        return $this;
    }

    public function groupBy($col)
    {
        $colExpr = self::_backQuote($col);
        return $this->groupByExpr($colExpr);
    }

    public function groupByExpr($colExpr)
    {
        $this->_groupbys[] = $colExpr;
        return $this;
    }

    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    public function orderByDESC($field)
    {
        $field = self::_backQuote($field);
        return $this->orderByExpr("$field DESC");
    }

    public function orderByASC($field)
    {
        $field = self::_backQuote($field);
        return $this->orderByExpr("$field ASC");
    }

    public function orderByExpr($expr)
    {
        $this->_orderbys[] = $expr;
        return $this;
    }

    public function select($col)
    {
        $argNum = func_num_args();
        if ($argNum == 1) {
            $this->selectExpr(self::_backQuote($col));
        } elseif ($argNum == 2) {
            $as = func_get_arg(1);
            $expr = self::_backQuote($col)." AS ".self::_backQuote($as);
            $this->selectExpr($expr);
        }
        return $this;
    }

    public function selectExpr($expr)
    {
        $this->_selects[] = $expr;
        return $this;
    }

    public function selectMany()
    {
        $cols = func_get_args();
        foreach ($cols as $col) {
            if (is_string($col)) {
                $this->select($col);
            } elseif (is_array($col)) {
                foreach ($col as $key => $value) {
                    if (is_int($key)) {
                        $this->select($col);
                    } elseif (is_string($key)) {
                        $this->select($key, $value);
                    }
                }
            }
        }
        return $this;
    }

    public function selectManyExpr()
    {
        $colExprs = func_get_args();
        return $this->_selectManyExpr($colExprs);
    }

    private function _selectManyExpr($arr)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $this->_selectManyExpr($value);
            } elseif (is_string($value)) {
                $this->selectExpr($value);
            }
        }
        return $this;
    }

    public function distinct()
    {
        $this->_distinct = true;
        return $this;
    }

    public function rawQuery($str, $values)
    {
        $this->_raw = true;
        $this->_query = array($str => $values);
    }

    private function _buildWhere()
    {
        if ($this->_wheres) {
            list($str, $values) = self::_buildPredicates($this->_wheres);
            return array('WHERE '.$str, $values);
        }
        return array('', array());
    }

    private function _buildHaving()
    {
        if ($this->_havings) {
            list($str, $values) = self::_buildPredicates($this->_wheres);
            return array('HAVING '.$str, $values);
        }
        return array('', array());
    }

    private function _buildField()
    {
        if ($this->_count) {
            return $this->_buildCount();
        }
        if ($this->_selects) {
            return implode(', ', $this->_selects);
        }
        return '*';
    }

    private function _buildCount()
    {
        if ($this->_selects) {
            $field = current($this->_selects);
            return "COUNT($field)";
        } else {
            return "COUNT($this->_primaryKey)";
        }
    }

    private static function _buildPredicates($raws)
    {
        $strs = array();
        $values = array();
        foreach ($raws as $key => $kv) {
            $strs[] = $str = key($kv);
            $vals = current($kv);
            $values = array_merge($values, $vals);
        }
        $str = implode(' AND ', $strs);
        return array($str, $values);
    }

    private function _buildOrderBy()
    {
        if ($this->_orderbys) {
            return 'ORDER BY '.implode(',', $this->_orderbys);
        }
        return '';
    }

    private function _buildGroupBy()
    {
        if ($this->_groupbys) {
            return 'GROUP BY '.implode(',', $this->_groupbys);
        }
        return '';
    }

    public function findOne()
    {
        if (func_num_args() == 1) {
            $this->whereId(func_get_arg(0));
        }
        $this->limit(1);
        $data = $this->_fetch();
        $value = current($data);
        return $data ? new DataWrapper($this->_table, $value, $value[$this->_primaryKey]) : false;
    }

    public function findMany()
    {
        $data = $this->_fetch();
        $ret = array();
        foreach ($data as $key => $value) {
            if (array_key_exists($this->_primaryKey, $value)) {
                $id = $value[$this->_primaryKey];
                $ret[$id] = new DataWrapper($this->_table, $value, $id);
            } else {
                $ret[] = new DataWrapper($this->_table, $value);
            }
        }
        return $ret;
    }

    public function findArray()
    {
        return $this->_fetch();
    }

    public function count()
    {
        $this->_count = true;
        $data = $this->_fetch();
        return current($data[0]);
    }

    public function min($col)
    {
        $expr = 'MIN('.self::_backQuote($col).')';
        $this->selectExpr($expr);
    }

    public function max($col)
    {
        $expr = 'MAX('.self::_backQuote($col).')';
        $this->selectExpr($expr);
    }

    public function sum($col)
    {
        $expr = 'SUM('.self::_backQuote($col).')';
        $this->selectExpr($expr);
    }

    public function avg($col)
    {
        $expr = 'AVG('.self::_backQuote($col).')';
        $this->selectExpr($expr);
    }

    public function join($table, $on)
    {
        if (is_array($on)) {
            $on = self::_backQuote($on[0]).$on[1].self::_backQuote($on[2]);
        }
        if (func_num_args() == 3) {
            $alias = func_get_arg(2);
            $this->_table($table, $alias);
            $table = $alias;
        }
        $this->_joins[] = array($table, $on);
        return $this;
    }

    public function tableAs($name)
    {
        $this->_as = $name;
        return $this;
    }

    private function _table($table, $alias)
    {
        $this->_tables[] = self::_backQuoteWord($table).' AS '.self::_backQuoteWord($alias);
        return $this;
    }

    private function _buildTable()
    {
        $my = self::_backQuoteWord($this->_table);
        if ($this->_as) {
            $my .= ' AS '.self::_backQuoteWord($this->_as);
        }
        array_unshift($this->_tables, $my);
        return implode(', ', $this->_tables);
    }

    private function _buildSelectSql()
    {
        if ($this->_raw) {
            return $this->_query;
        }
        $field = $this->_buildField();
        $table = $this->_buildTable();
        list($where, $whereVals) = $this->_buildWhere();
        list($having, $havingVals) = $this->_buildHaving();
        $groupBy = $this->_buildGroupBy();
        $orderBy = $this->_buildOrderBy();
        $sql = "SELECT"
            .($this->_distinct ? ' DISTINCT' : '')
            ." $field FROM $table"
            .($where ? " $where" : '')
            .($groupBy ? " $groupBy" : '')
            .($having ? " $having" : '')
            .($orderBy ? " $orderBy" : '')
            ." LIMIT $this->_limit OFFSET $this->_offset";
        $values = array_merge($whereVals, $havingVals);
        if (self::$_config['logging']) {
            self::_log($sql, $values);
        }
        return array($sql, $values);
    }

    private static function _log($sql, $values)
    {
        self::$_rawLogs[] = array($sql, $values);
        while ($values) {
            $sql = preg_replace('/\?/', "'".array_pop($values)."'", $sql);
        }
        self::$_logs[] = self::$_last_sql = $sql;

        // just for test
        if (self::$_config['debug']) {
            $fname = 'log.sql'; // for windows
            if (! @touch($fname)) {
                $fname = '/tmp/'.$fname; // for linux and mac
            }
            file_put_contents($fname, implode("\n", self::$_logs));
        }
    }

    public static function getLastSql()
    {
        return self::$_last_sql;
    }

    public static function getLogs()
    {
        self::$_logs;
    }

    private function _fetch()
    {
        list($sqlStr, $values) = $this->_buildSelectSql();

        $stmt = self::_execute($sqlStr, $values);

        $ret = array();
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (isset($row[$this->_primaryKey])) {
                $ret[$row[$this->_primaryKey]] = $row;
            } else {
                $ret[] = $row;
            }
        }
        return $ret ?: array();
    }

    private static function _execute($sqlStr, $values)
    {
        $db = self::db();
        $stmt = $db->prepare($sqlStr);
        foreach ($values as $key => $value) {
            $stmt->bindValue($key+1, $value);
        }
        $stmt->execute();
        if (intval($stmt->errorCode())) {
            print_r($stmt->errorInfo());
            throw new Exception('db error: '.$stmt->errorCode());
        }
        return $stmt;
    }

    public function update($arr)
    {
        $sql = "UPDATE $this->_table SET ";

        $exprs = array();
        $values = array();
        foreach ($arr as $key => $value) {
            if ($value instanceof Expression) {
                $val = $value->sql();
                $values = array_merge($values, $value->$values);
            } else {
                $val = '?';
                $values[] = $value;
            }
            $exprs[] = self::_backQuoteWord($key)."=$val";
        }

        $sql .= implode(', ', $exprs);
        list($where, $whereVals) = self::_buildWhere();
        $sql .= $where ? " $where" : '';
        $values = array_merge($values, $whereVals);
        self::_execute($sql, $values);
    }

    public function insert($arr)
    {
        $sql = "INSERT INTO $this->_table ";

        $keys = array();
        $values = array();
        $vals = array();
        foreach ($arr as $key => $value) {
            $keys[] = self::_backQuoteWord($key);
            if ($value instanceof Expression) {
                $vals[] = $value->sql();
                $values = array_merge($values, $value->$values);
            } else {
                $vals[] = '?';
                $values[] = $value;
            }
        }

        $sql .= '('.implode(', ', $keys).') VALUES ('.implode(', ', $vals).')';
        self::_execute($sql, $values);
        return self::$_db->lastInsertId();
    }

    public function delete()
    {
        $sql = "DELETE FROM $this->_table ";
        list($where, $whereVals) = self::_buildWhere();
        $sql .= $where ? " $where" : '';
        self::_execute($sql, $whereVals);
    }

    public function table()
    {
        if (isset(static::$table)) {
            return static::$table;
        }
        $s = preg_replace('/[A-Z]/', '_$0', get_called_class());
        return substr($s, strrpos($s, '_model'));
    }

    public function t()
    {
        return self::table();
    }

    public function pkey()
    {
        if (isset(static::$pkey)) {
            return static::$pkey;
        }
        return static::table().'_id';
    }
    
    public function get($id)
    {
        return ORM::forTable(static::table())->findOne($id);
    }

    public function edit($id, $args)
    {
        $data = $this->filter($args);
        $valid_data = $this->valid($data);
        return ORM::forTable(static::table())->where(static::$pkey, $id)->update($valid_data);
    }

    public function add($args)
    {
        $data = $this->filter($args);
        $valid_data = $this->valid($data);
        return ORM::forTable(static::table())->insert($valid_data);
    }

    public function delete($id)
    {
        return ORM::forTable(static::table())->where(static::$pkey, $id)->delete();
    }
    
}

/**
* Row
*/
class DataWrapper implements ArrayAccess
{
    private $_info;

    public function __construct($info)
    {
        $this->_info  = $info;
    }

    // 给予程序员通过访问这个对象的属性来取数据的能力
    public function __get($name) 
    {
        return $this->get($name);
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->_info)) {
            throw new Exception("no '$name' when get in class " . get_called_class());
        }
        return $this->_info[$name];
    }

    public function offsetExists($offset)
    {
        return $this->_isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this;
    }

    public function offsetUnset($offset)
    {
        return $this;
    }

    public function __isset($name)
    {
        return $this->_isset($offset);
    }

    public function _isset($name)
    {
        return isset($this->_info[$name]);
    }

    public function toArray()
    {
        return $this->_info;
    }
}
