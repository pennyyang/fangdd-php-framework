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
        if ($this->_rules) {
            // 解析规则（阻断性）
            foreach ($this->_rules as $rule) {
                $method_match = $rule['method'] === null || in_array($_SERVER['REQUEST_METHOD'], $rule['method']);
                if ($method_match && preg_match($rule['regex'], $this->_url, $matches)) {
                    
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

                    $contr = $rule['contr'];
                    $act = $rule['act'];
                    foreach ($rule['names'] as $name) {
                        $contr = preg_replace('/\{\$'.$name.'\}/', $_GET[$name], $contr);
                        $act = preg_replace('/\{\$'.$name.'\}/', $_GET[$name], $act);
                    }

                    $result = $this->_execute($contr, $act);
                    break;
                }
            }
        } else {
            // 默认的路由规则 /controller/action
            // 默认 404 page404Controller::indexAction()
            $arr = explode('/', $this->_url);
            unset($arr[0]);
            $contr = isset($arr[1]) && $arr[1] ? $arr[1] : 'index';
            $act = isset($arr[2]) && $arr[2] ? $arr[2] : 'index';
            try {
                $result = $this->_execute($contr, $act);
            } catch (Exception $e) {
                if ($e->getCode() == 404) {
                    $result = $this->_execute('page404', 'index');
                } else {
                    throw $e;
                }
            }
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
        $cf = APP_ROOT . 'controller/' . $controller . '.php';
        if (!file_exists($cf)) {
            throw new Exception('Controller file: '.$cf.' not exists', 404);
        }
        require $cf;
        $c = new $controller;
        $result = $c->$action();
        return $result;
    }

    /**
     * 新增一条路由规则
     * $router->rule('GET', '/user/[:id]', array('user', 'view'))
     * $router->rule('POST', '/user/[:id]', array('user', 'edit'))
     * $router->rule('/user/', array('user', 'list'))
     * $router->rule('*', array('page404', 'index'))
     * 第一个参数可以不填
     * @param $method HTTP方法 'GET'|'POST'|'PUT'|'DELETE'
     * @param $rule URL规则，如 /user/[:id]，其中方括号冒号开头代指一个参数，放到 $_GET 数组中
     * @param $ca 数组 array('控制器', 'Action')
     */
    public function rule()
    {
        $args_num = func_num_args();
        if ($args_num == 2) {
            return $this->_rule(null, func_get_arg(0), func_get_arg(1));
        }
        if ($args_num == 3) {
            return $this->_rule(func_get_arg(0), func_get_arg(1), func_get_arg(2));
        }
    }
    private function _rule($method, $rule, $ca)
    {
        if ($rule === '*') {
            $regex = '.*';
        } else {
            $regex = preg_replace('/\[:[a-zA-Z][a-zA-Z\d_]*\]/', '([^/]+)', $rule);
        }
        preg_match_all('/\[:([a-zA-Z][a-zA-Z\d_]*)\]/', $rule, $matches);
        $this->_rules[] = array(
            'method' => is_string($method) ? array($method) : $method,
            'regex' => '/^'.str_replace('/', '\/', $regex).'$/',
            'names' => $matches[1],
            'contr' => $ca[0],
            'act' => $ca[1],
        );
    }
}

