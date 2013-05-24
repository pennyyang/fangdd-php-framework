<?php
/**
 * 路由类
 * 
 * 下一步可以实现根据返回值自动渲染相应的模版文件
 * 模版文件位置：/view/template/controller/action.phtml
 *
 * 下下步，可以指定路由规则
 * rule()
 * rules()
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
            if (preg_match('/^'.$regex.'$/', $this->_url, $matches)) {
                
                // 将参量压入 $_GET
                foreach ($matches as $key => $value) {
                    if ($key) {
                        $name = $rule['names'][$key-1];
                        if (!isset($_GET[$name])) {
                            $_GET[$name] = $value;
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
     * 第一个参数是HTTP方法
     * 第二个参数是URL规则，其中方括号冒号开头代指一个参数，放到 $_GET 数组中
     * 第三个参数是一个数组 array('控制器', 'Action')
     */
    public function rule($method, $rule, $ca)
    {
        $regex = preg_replace('/\[:[a-zA-Z][a-zA-Z\d_]*\]/', '([^/]+)', $rule);
        preg_match_all('/\[:([a-zA-Z][a-zA-Z\d_]*)\]/', $rule, $matches);
        $this->_rules[] = array(
            'regex' => $regex, 
            'names' => $matches[1],
            'contr' => $ca[0],
            'act' => $ca[1],
        );
    }

}

