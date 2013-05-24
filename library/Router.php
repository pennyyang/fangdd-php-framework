<?php
/**
 * 路由类
 * 
 * 根据返回值自动渲染相应的模版文件
 * 模版文件位置：/view/template/controller/action.phtml
 * 可以指定路由规则
 */
class Router {

    private $_url;
    private $_rules = array();

    public function __construct($url)
    {
        $this->_url = $url;
    }

    /**
     * 分发函数
     * 调用此函数时执行 action 方法
     * default indexController::indexAction()
     */
    public function dispath()
    {
        foreach ($this->_rules as $rule) {
            $regex = str_replace('/', '\/', $rule['regex']);
            if ($_SERVER['REQUEST_METHOD'] == $rule['method'] && preg_match('/^'.$regex.'$/', $this->_url, $matches)) {
                
                // 将参量压入 $_GET
                foreach ($matches as $key => $value) {
                    if ($key) {
                        $name = $rule['names'][$key-1];
                        $_GET[$name] = $value;
                        if ($rule['method'] == 'POST') {
                            $_POST[$name] = $value;
                        }
                    }
                }

                $result = $this->_execute($rule['contr'], $rule['act']);
                $rule_exec = 1;
                break;
            }
        }
        if (!isset($rule_exec)) {
            // 默认的路由规则 /controller/action
            $arr = explode('/', $this->_url);
            unset($arr[0]);
            $contr = isset($arr[1]) && $arr[1] ? $arr[1] : 'index';
            $act = isset($arr[2]) && $arr[2] ? $arr[2] : 'index';
            $result = $this->_execute($contr, $act);
        }
        if (is_array($result)) {
            $view = new View($result);
            $view->template($contr.'/'.$act);
            return $view;
        }
        return $result;
    }

    private function _execute($contr, $act)
    {
        $controller = $contr.'Controller';
        $action = $act . 'Action';
        require APP_ROOT . 'controller/' . $controller . '.php';
        $c = new $controller;
        $result = $c->$action();
        return $result;
    }

    /**
     * 新增一条路由规则
     * $router->rule('GET', '/user/[:id]', array('user', 'view'))
     * @param HTTP方法 'GET'|'POST'|'PUT'|'DELETE'
     * @param URL规则，如 /user/[:id]，其中方括号冒号开头代指一个参数，放到 $_GET 数组中
     * @param 数组 array('控制器', 'Action')
     */
    public function rule($method, $rule, $ca)
    {
        $regex = preg_replace('/\[:[a-zA-Z][a-zA-Z\d_]*\]/', '([^/]+)', $rule);
        preg_match_all('/\[:([a-zA-Z][a-zA-Z\d_]*)\]/', $rule, $matches);
        $this->_rules[] = array(
            'method' => $method,
            'regex' => $regex, 
            'names' => $matches[1],
            'contr' => $ca[0],
            'act' => $ca[1],
        );
    }
}

