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

    /**
     * 新增一条路由规则
     * $router->rule('GET', '/user/[:id]', array('user', 'view'))
     * 第一个参数是HTTP方法
     * 第二个参数是URL规则，其中方括号冒号开头代指一个参数，放到 $_GET 数组中
     * 第三个参数是一个数组 array('控制器', 'Action')
     */
    public function rule()
    {

    }

}

