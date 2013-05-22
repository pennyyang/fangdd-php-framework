<?php
/**
* php 视图渲染类
*/
class PhpRender
{
    private $_data = array();
    private $_layout;
    private $_tpl = 'index.phtml';

    public function __get($var)
    {
        return isset($this->_data[$var]) ? $this->_data[$var] : null;
    }

    public function __set($var, $value)
    {
        $this->_data[$var] = $value;
        return $this;
    }

    public function vars()
    {
        return $this->_data;
    }

    public function __construct($data = array())
    {
        $this->_data = $data;
    }

    public function layout($tpl)
    {
        $this->_layout = $tpl;
    }

    public function template($tpl)
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
        extract($this->_data);
        if ($this->_layout) {
            include APP_ROOT.'view/layout/'.$this->_layout;
        } else {
            include APP_ROOT.'view/template/'.$this->_tpl;
        }
    }

    public function yield()
    {
        $old_layout = $this->_layout;
        $this->_layout = null;
        $this->render();
        $this->_layout = $old_layout;
    }

}
