<?php

/**
 * 下一步可以实现根据返回值自动渲染相应的模版文件
 * 模版文件位置：/view/template/controller/action.phtml
 *
 * 下下步，可以指定路由规则
 * rule()
 * rules()
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
        $contr = isset($arr[1]) && $arr[1] ? $arr[1] : 'index';
        $controller = $contr.'Controller';
        $act = isset($arr[2]) && $arr[2] ? $arr[2] : 'index';
        $action = $act . 'Action';
        require_once APP_ROOT . 'controller/' . $controller . '.php';
        $c = new $controller;
        $result = $c->$action();
        if (is_array($result)) {
            $view = new View($result);
            $view->template($contr.'/'.$act);
            return $view;
        }
        return $result;
    }

}

