<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел

Главный модуль AD фреймворка. В нём происходит получение параметров, входящих данных.
Основные методы:
  init()    - инициальизация, получение входящих параметров и конфигураций проекта
  start()   - создание экземпляра контроллера и запуск экшена
*/
class AD 
{
	static protected $dir;
	static private   $conf;
	static public    $Router;
	static private   $GET;
	static private   $POST;
	static private   $REQUEST;
	static private   $HEADERS;
	static private   $Registry = [];
	static public 	 $errors = [];
	static protected 	 $routsCache = [];

	/**
	 * Предупреждение
	 * @param $error - string 
	 * @return CDbCommand
 	*/

	static public function setError($modul,$error)
	{
		if (empty(self::$errors[$modul])){
			self::$errors[$modul] = [];
		}
		self::$errors[$modul][] = $error;
	}

	static public function getLastError($modul){
		$er = array_pop(self::$errors[$modul]);
		return isset($er)?$er: 'unknownerror';
	}



	static public function dir()
	{
		return self::$dir;
	}

	/**
	 * Отдаёт список конфигураций по переданным ключам
	 * @param $argv = func_get_args
	 * @return config array
 	*/
	static public  function config()
	{
		$argv = func_get_args();
		if (empty($argv)){
			return self::$conf;
		} else {
			if (count($argv) === 1){
				return self::$conf[$argv[0]];
			}else {
				$list = [];
				foreach($argv as $a){
					$list[$a] = self::$conf[$a];
				}
			}
			return $list;
		}
	}

	static public function init($dir)
	{
		self::$dir = $dir;
		self::$conf = include ROOT.'/config.php' ;
		self::$Router = new ad_routes();
		self::$GET = $_GET; 
		self::$POST = $_POST; 
		self::$REQUEST = array_merge($_REQUEST,self::$Router->getRequest()); 
		self::$HEADERS = apache_request_headers();

	}

	static private function start_controller($controller,$action) {
		if (file_exists(self::$dir."/controller/".$controller.".php")){	
			include_once(self::$dir."/controller/".$controller.".php");
			//Создаём экземпляр класса
			$class = new $controller($controller);
			if (method_exists($class ,$action."_action")){
				$act = $action."_action"; 
				//Запускаем экшен контроллера
				$class->$act();
			}	else {
				self::setError('app','not found method '.$action.'_action , controller '.$controller);
			}	

			return true;

		}	else {
			return false;	
		}
	}

	static public function start()
	{
		//Удалёяем глобальные массивы
		unset($_GET);
		unset($_POST);
		unset($_REQUEST);

		$Controller = self::$Router->getController();
		$Action = self::$Router->getAction();
		//Определяем контроллер и экшен 
		$controller = $Controller != "" ? $Controller  :  self::config("default_controller");
		$action     = $Action     != "" ? $Action      :  self::config("default_action");

		// print_r(array($action,$controller));
		// exit();

		if ($action !== false){
			if (!self::start_controller($controller,$action)){

				self::initRouts();
				
				self::start_controller(self::config("404_controller"),self::config("404_action"));
				
			}
		}



	}
	/* отменяем автолоадер и удаляем обьект */
	static public function destroy()
	{
		spl_autoload_unregister(array('Autoloader', 'loadPackages'));
		unset($this);
	}

	static private function _vaid($array)
	{
		//валидация массива параметров
		if (empty($array) && intval($array) === 0){
			//Ели значения нет то возвращаем false //
			return false;
		}
		return $array;
	}


	/* Функции возвращают входящие параметры по ключу */
	static public function get($par = false)
	{
		if ($par === false){
			return self::_vaid(self::$GET);
		} else {
			return self::_vaid(self::$GET[$par]);
		}
	}


	static public function post($par = false)
	{
		if ($par === false){
			return self::_vaid(self::$POST);
		} else {
			return self::_vaid(self::$POST[$par]);
		}
	}

	static public function headers($par = false)
	{
		if ($par === false){
			return self::_vaid(self::$HEADERS);
		} else {
			return self::_vaid(self::$HEADERS[$par]);
		}
	}


	


	static public function request($par = false)
	{
		if ($par === false){
			return self::_vaid(self::$REQUEST);
		} else {
			return self::_vaid(!empty(self::$REQUEST[$par])?self::$REQUEST[$par]:[]);
		}
	}


	/**
	 * Отдаёт экземпляр обьекта по имени
	 * @param $classname - имя класса, 
	 * @param  $par - массив передаётся в конструктор,
	 * @param  $n - новый экземпляр или из кеша.
	 * @return config array
 	*/
 	
	static public function DI($classname,$par = array(),$n = false) {

		if(!$n){
			if(!empty(self::$Registry[$classname])){
				return self::$Registry[$classname];
			} else {
				$_c = new $classname($par);
				return self::$Registry[$classname] = $_c;
			}
		} else {
			$_c = new $classname($par);
			return self::$Registry[$classname] = $_c;
		}

	}

	/** TODO - сделать кеширование мепа */
	static public function initRouts(){
		$request = new ad_request(AD::get(),AD::post(),AD::headers());


		foreach (glob(self::$dir.self::$conf['routs_path']."*.php") as $routs) {
			include_once $routs;
		}

		foreach (self::$routsCache as $route) {
			if ($route->test($request))
				return;
		}
	
	}


	static public function route($method, $url, $fn){
		self::$routsCache[] = new ad_route($method, $url, $fn);
	}

}
?>