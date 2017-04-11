<?php
/*
Версия файла: 1.2
Автор : Найдёнов Павел
*/

return array(
  'sitename' => 'sitename',
  'encode' => 'utf-8',
  'cookietime' => 3600,
  'version' => '1.0.2 ',
  '404_controller'  => 'index',
  '404_action'      => 'e404',
  'default_controller' => 'index',
  'default_action' => 'index',
  'default_template'  => 'main',
  'templatepath' => '/viewer/',

  'phpcache' => array('path'=>'cache/php/',
                      'time'=>60000,
                      'metod'=>'serialize'),      //serialize, html, json 

  'tplcache' => array('path'=>'cache/tpl/',
                      'time'=>60000,
                      'metod'=>'html'),
  'db' =>    array(
                  'host'      => 'localhost',
                  'user'      => 'root',
                  'pass'      => 'root',
                  'db'        => 'main',
                  'port'      => NULL,
                  'socket'    => NULL,
                  'pconnect'  => FALSE,
                  'charset'   => 'utf8',
                  'errmode'   => 'error', //or exception
                  'exception' => 'Exception', //Exception class name
                  'name'      =>  'main'
                )
);