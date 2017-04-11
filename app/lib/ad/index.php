<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел
*/
session_start();
define ("AD", __DIR__ );
define ("DATA", date("Y-m-d") ); //'2015-01-31'+
define ("ROOT",  AD.'/../../' );
define ("DIR",AD.'/../');
define ("LANG",'ru');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once('class/autoloader.php');
require_once("ad.php");

AD::init(ROOT);

?>