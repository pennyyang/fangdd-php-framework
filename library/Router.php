<?php

/*
 * 下一步可以实现根据返回值自动渲染相应的模版文件
 * 模版文件位置：/view/template/controller/action.phtml
 */

class Router {

    private $url;

    public function __construct($url) {
        $this->url = $url;
    }

    /**
     * default indexController::indexAction()
     */
    public function dispath() {
        $arr = explode('/', $this->url);
        unset($arr[0]);
        $controller = (isset($arr[1]) && $arr[1] ? $arr[1] : 'index').'Controller';
        $action = (isset($arr[2]) && $arr[2] ? $arr[2] : 'index') . 'Action';
        require_once APP_ROOT . 'controller/' . $controller . '.php';
        $a = new $controller;
        $result = $a->$action();
        return $result;
    }

}

