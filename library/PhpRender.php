<?php
/**
* php 视图渲染类
*/
class PhpRender
{
    private $_data = array();
    private $_tpl = 'index.phtml';

    protected function __get($var)
    {
        return isset($this->_data[$var]) ? $this->_data[$var] : null;
    }

    public function __set($var, $value)
    {
        $this->_data[$var] = $value;
        return $this;
    }

    public function __construct($data = array())
    {
        $this->_data = $data;
    }

    public function setTemplate($tpl)
    {
        $this->_tpl = $tpl;
    }

    public function render($tpl = null, $data = null)
    {
        if ($tpl !== null) {
            $this->_tpl = $tpl;
        }
        if ($data !== null) {
            $this->_data = $data;
        }
        extract($this->$data);
        include APP_ROOT.'view/template/'.$this->_tpl;
    }

    public static function init($root)
    {
        self::$_data = array_merge(
            include $root.'app.config.php',
            include $root.ENV.'.config.php'
        );
    }
}
