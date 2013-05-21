<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Router {

    private $url;

    public function __construct($url) {
        $this->url = $url;
    }

    public function dispath() {
        $arr = explode('/', $this->url);
        unset($arr[0]);
        $condition = $arr[1] . 'Controller.php';
        $method = $arr[2] . 'Action';
        $u = 'x.me' . '/' . $condition;
        require_once APP_ROOT . 'controller/' . $condition;
        $a = new $arr[1];
        $result = $a->$method();
        return $result;
    }

}

?>
