<?php
/**
 * 项目配置文件
 * 全局的配置
 */
return array(
    'application_name' => '',
    'debug' => true,
    'router' => array(
        array('/', array('index', 'index')),
        array('GET', '/test/[:id]', array('test', 'index')),
        array('/[:controller]/[:action]', array('{$controller}', '{$action}')),
        array('GET', '*', array('page404', 'index'))
    )
);