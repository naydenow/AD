<?php 
namespace np;
class lang {
	static private $data = array();
	static private $dataForm = array();
	static public function get($controller,$key)
	{
		if(empty(self::$data[$controller])){
			self::$data[$controller] = include_once  ROOT.'lang/'.LANG.'/controller/'.$controller.'.php';
		}
		return self::$data[$controller][$key];
	} 

	static public function getLangForm($modul,$form)
	{
		$name =$modul.$form;
		if(empty(self::$dataForm[$name])){
			self::$dataForm[$name] = include_once  ROOT.'lang/'.LANG.'/form/'.$modul.'/'.$form.'.php';
		}
		return self::$dataForm[$name];
	} 

}