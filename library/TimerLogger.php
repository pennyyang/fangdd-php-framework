<?php
/**
* 计时器
*/
class TimerLogger
{
    private $_start;
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
        $this->_logger = new Logger();
        $this->_start = microtime(true);
    }

    public function log($level = 'debug')
    {
        $t = microtime(true) - $this->_start;
        return $this->_logger->log($this->_name.' '.intval($t*1000).' ms', $level);
    }
}
