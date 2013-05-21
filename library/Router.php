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
        $method = $arr[2] . 'Action';
        require_once APP_ROOT . 'controller/' . $arr[1] . '.php';
        $a = new $arr[1];
        $result = $a->$method();
        return $result;
    }

}

