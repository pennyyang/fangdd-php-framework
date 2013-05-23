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
* ORM
* 对应一张表
*/
class ORM
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

    private $_pkey;
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
    private $_raw = false;
    private $_query;

    public static function config($key)
    {
        if (func_num_args() == 1) {
            if (is_string($key)) {
                self::$_config['dsn'] = $key;
            } elseif (is_array($key)) {
                foreach ($key as $k => $v) {
                    self::$_config[$k] = $v;
                }
            }
        } else {
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
        if ($pkey === null) {
            $pkey = 'id';
        }
        return new self($table, $pkey);
    }

    protected function __construct($table, $pkey)
    {
        $this->_table = $table;
        $this->_pkey = $pkey;
    }

    public function from($table)
    {
        if (is_array($table)) {
            return $this->from(current($table))->alias(key($table));
        }
        $this->_table = $table;
        return $this;
    }

    /**
     * where('username', 'Jack')
     */
    public function where($key, $op = null, $value = null)
    {
        if ($op !== null && $value === null) {
            return $this->where($key, '=', $op);
        }
        if ($key instanceof Expression && $op === null && $value = null) {
            $expr = $key->sql();
            $values = $key->values();
        } else {
            $key = self::_backQuote($key);
            if ($value instanceof Expression) {
                $expr = "$key $op ".$value->sql();
                $values = $value->values();
            } else {
                $expr = "$key $op ?";
                $values = array($value);
            }
        }
        $this->_wheres[] = array($expr, $values);
        return $this;
    }

    public function whereId($id)
    {
        return $this->where($this->_pkey, '=', $id);
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

    public function having($key, $op, $value = null)
    {
        if ($value === null) {
            return $this->having($key, '=', $op);
        }
        if ($key instanceof Expression) {
            $expr = $key->sql();
            $values = $key->values();
        } else {
            $key = self::_backQuote($key);
            if ($value instanceof Expression) {
                $expr = "$keys $op ".$value->sql();
                $values = $value->values();
            } else {
                $expr = "$keys $op ?";
                $values = array($value);
            }
        }
        $this->_havings[] = array($expr, $values);
        return $this;
    }

    public function groupBy($col)
    {
        if ($col instanceof Expression) {
            $this->_groupbys[] = $col->sql();
        } else {
            $this->_groupbys[] = self::_backQuote($col);
        }
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
        return $this->orderBy("$field DESC");
    }

    public function orderByASC($field)
    {
        $field = self::_backQuote($field);
        return $this->orderBy("$field ASC");
    }

    public function orderBy($expr)
    {
        $this->_orderbys[] = $expr;
        return $this;
    }

    public function select()
    {
        return $this->findMany();
    }

    public function column($col, $as = null)
    {
        $argNum = func_num_args();
        if ($as === null) {
            if ($col instanceof Expression) {
                $col = $col->sql();
            } elseif (is_array($col)) {
                $col = self::_backQuote(key($col)).' AS '.self::_backQuote(current($col));
            } else {
                $col = self::_backQuote($col);
            }
        } else {
            $col = self::_backQuote($col).' AS '.self::_backQuote($as);
            $this->_selects[] = $expr;
        }
        $this->_selects[] = $col;
        return $this;
    }

    public function columns($cols)
    {
        foreach ($cols as $col) {
            $this->col($col);
        }
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
                        $this->select($value);
                    } elseif (is_string($key)) {
                        $this->select($key, $value);
                    }
                }
            }
        }
        return $this;
    }

    public function distinct()
    {
        $this->_distinct = true;
        return $this;
    }

    /**
     * query('select * frorm user where user_id=?', array('3'))->select()
     */
    public function query($str, $values = array())
    {
        $this->_raw = true;
        if (!($str instanceof Expression)) {
            $str = new Expression($str, $values);
        }
        $this->_query = $str;
        return $this;
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
            return "COUNT($this->_pkey)";
        }
    }

    private static function _buildPredicates($raws)
    {
        $strs = array();
        $values = array();
        foreach ($raws as $kv) {
            $strs[] = $str = $kv[0];
            $vals = $kv[1];
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

    public function find()
    {
        if (func_num_args() == 1) {
            $this->whereId(func_get_arg(0));
        }
        $this->limit(1);
        $data = $this->_fetch();
        $value = current($data);
        return $data ? new DataWrapper($value) : false;
    }

    public function findMany()
    {
        $data = $this->_fetch();
        $ret = array();
        foreach ($data as $key => $value) {
            if (array_key_exists($this->_pkey, $value)) {
                $id = $value[$this->_pkey];
                $ret[$id] = new DataWrapper($value);
            } else {
                $ret[] = new DataWrapper($value);
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

    /**
     * join(array('u' => 'user'), array('u.id', 'blog.user_id'))
     */
    public function join($table, $on)
    {
        if (is_array($table)) {
            $alias = key($table);
            $table = current($table);
            $this->_table($table, $alias);
        } else {
            $this->_table($table);
        }
        if (is_array($on)) {
            $left = self::_backQuote($on[0]);
            if (count($on) == 2) {
                $op = '=';
                $right = self::_backQuote($on[1]);
            } else {
                $op = $on[1];
                $right = self::_backQuote($on[2]);
            }
            $on = $left.$op.$right;
        }
        $this->_joins[] = array($table, $on);
        return $this;
    }

    public function alias($name)
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
        if ($this->_raw) {
            $sqlStr = $this->_query->sql();
            $values = $this->_query->values();
        } else {
            list($sqlStr, $values) = $this->_buildSelectSql();
        }

        $stmt = self::_execute($sqlStr, $values);

        $ret = array();
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (isset($row[$this->_pkey])) {
                $ret[$row[$this->_pkey]] = $row;
            } else {
                $ret[] = $row;
            }
        }
        return $ret ?: array();
    }

    public function execute()
    {
        if ($this->_raw) {
            return self::_execute($this->_query->sql(), $this->_query->values());
        }
        return false;
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
            if (self::$_config['debug']) {
                var_dump($sqlStr);
                var_dump($stmt->errorInfo());
            }
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
}

/**
* 行数据包装器
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
        return $this->set($offset, $value);
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

    public function set($name, $value)
    {
        $this->_info[$name] = $value;
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function toArray()
    {
        return $this->_info;
    }
}
