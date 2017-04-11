<?php 
namespace np;
class lang {
	static private $data = array();
	static private $dataForm = array();
	static public function get($key, $path)
	{
		if(empty(self::$data[$path])){
			self::$data[$path] = include_once  ROOT.'lang/'.LANG.$path.'.php';
		}
		return self::$data[$path][$key];
	} 

	static public function getLangForm($modul,$form)
	{
		$name =$modul.$form;
		if(empty(self::$dataForm[$name])){
			self::$dataForm[$name] = include_once  ROOT.'lang/'.LANG.'/form/'.$modul.'/'.$form.'.php';
		}
		return self::$dataForm[$name];
	} 
	static public function getLangController($controller,$key)
	{
		if(empty(self::$data[$controller])){
			self::$data[$controller] = include_once  ROOT.'lang/'.LANG.'/controller/'.$controller.'.php';
		}
		return self::$data[$controller][$key];
	} 

}