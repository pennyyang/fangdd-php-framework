<?php
/**
* 视图的基类
*/
class View
{
    protected $_render;

    public function __construct($data = array())
    {
        $this->_render = new PhpRender($data);
    }

    public function layout($tpl)
    {
        $this->_render->layout($tpl.'.phtml');
    }

    public function template($tpl)
    {
        $this->_render->template($tpl.'.phtml');
    }

    public function render()
    {
        $this->_render->render();
    }
}
