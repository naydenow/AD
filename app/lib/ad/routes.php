<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел

При инициализации роутер парсит url строку для определения к какому методу какого контроллера идёт обращение.
Парсится строкка следующими способами:
http::\ad\index - в этом случае будет запущен контроллер index и метод по умолчанию в этом контроллере, 
метод который должен запустится по умолчанию указывается в файле конфигурации.

http::\ad\index\action1 -  в этом случае будет запущен контроллер index и его метод action1 

http::\ad\index\action1\1- в этом случае будет запущен контроллер index и его метод action1 
так же в контроллер будет передан параметр id котроый будет доступен из метода request класса AD, AD::request('id');

http::\ad\index\action1\ name\vasa\age\23\sex\m -  в этом случае будет запущен контроллер index и его метод action1 
так же в контроллер будут переданы параметры name => vasa, age=>23,
 sex=>m которые будут доступны из метода request класса AD, AD::request('key');
*/
class ad_routes 
{
	public $list = array(); // input parameters
	private $routes;
	function __construct()
	{
		$this->routes = include ROOT.'/routes.php';
		$this->list = $this->init();		
	} 

	/* Инициализация экземпларя обьекта, парсится url */
	public function init()
	{
		$masp = array();
		$masp['request'] = array();
		if (isset($_GET['method'])){
			$met = explode(":", $_GET['method']);
		
			if ($met[0]){
				$masp['controller'] = $met[0];
			}
			if ($met[1]){
				$masp['action'] = $met[1];
			}
		}

		if ($_SERVER['REQUEST_URI'] != '/') {
				$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
				$url_path = trim($url_path,'/');
		} else return $masp;

		if (!empty($this->routes[$url_path])){
			$url_path = $this->routes[$url_path];
		} else {
			foreach ($this->routes as $key => $value) {
				$st = strpos($key,'*'); 
				if($st !== false){//Звёздачка в роуте есть
					if(substr($url_path,0,$st)."*" == $key){
						$url_path = $value.'/'.substr($url_path,$st,9999);
						break;
					}
				}
			}
		}

	

		$qwer = array();
		$uri_parts = explode('/', trim($url_path, ' /'));
		$co =  count($uri_parts);

		if ($co >= 1 && $uri_parts[0] != ''){
			$masp['controller'] = $uri_parts[0];
		}

		if ($co >= 2){
			$masp['controller'] = $uri_parts[0];
			$masp['action'] 	= $uri_parts[1];
		}

		if ($co === 3){
			$masp['id'] 	= $uri_parts[2];
			$qwer['*'] 		= $uri_parts[2];
		}

		if ($co > 3 ){
			$o = 2;
			$cp = count($uri_parts)-1;
			while($cp > $o){
				$qwer[$uri_parts[$o++]] = $uri_parts[$o++];
			}
		}

		if (!empty($qwer)){
			$masp['request'] = $qwer;
		}

		return $masp;
	}

	/* Функции для получения параметров,  контроллера и экшена */
	public function getRequest(){
		return $this->list['request'];
	}
	public function getController(){
		return !empty($this->list['controller']) ? $this->list['controller'] : "";
	}
	public function getAction(){
		return !empty($this->list['action']) ? $this->list['action'] : ""; 
	}

}

?>