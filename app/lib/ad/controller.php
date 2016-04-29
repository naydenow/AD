<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел

Методы этого класса наследуют все контроллеры

Для того что бы вьюшка от ренерилась внутри  шаблона достаточно выполнить метод назначения шаблона  setTemplate()

Классически страница строиться 
				header
				{ viewer }
				footer

Возможно изменить порядок следующими способами
{controller:action}->view()->loadFooter()->loadHeader()->render();
 				footer
 				header
				{ viewer }


				

*/
class ad_controller extends ad_abstract_controller
{

	public $arData = array();
	private $cache = false;
	private $cachename;
	private $content = false;
	protected $noview = true;

	function __construct($par)
	{
		$this->name = $par;
		$this->tpname= $par;
	} 

	public function close($text)
	{
		$this->noview = false;
		echo $text;
	}

	/**
	 * Метод назначает шаблон
	 * @param $name - имя шаблона
	 * @return true || false
 	*/
	public function setTemplate($name = false)
	{
		$this->templateName =  $name !== false ? $name :  AD::config("default_template");
	}

	/**
	 * Метод отменяет шаблон
	 * @param $name - имя шаблона
	 * @return true || false
 	*/
	public function notTemplate()
	{
		$this->templateName = false;
	}

	public function header($url){
		header('Location: '.$url);
	}

	/* Метод подгружает header шаблона */
	public function loadHeader($name = false)
	{

		if (empty($this->templateName) && !$name){
			return $this;
		}
		if (!$name){
			$name = $this->templateName;
		}

		include_once(ROOT.'viewer/template/'.$name.'/header.php');

		return $this;
	}

	/* Метод подгружает footer шаблона */
	public function loadFooter($name = false)
	{
		if (empty($this->templateName) && !$name){
			return $this;
		}
		if (!$name){
			$name = $this->templateName;
		}

		include_once(ROOT.'viewer/template/'.$name.'/footer.php');
		return $this;
	}


	/**
	 * Метод вывода
	 * @return $this, ad_controller
 	*/
	public function render()
	{
		if (!!$this->cache){
			$this->cache->save($this->content);
		}

		if (!empty($this->templateName) && $this->templateName != false){
			$this->loadHeader();
		}

		echo $this->content;

		if (!empty($this->templateName) && $this->templateName != false){
			$this->loadFooter();
		}

		return $this;

	}

	private function _start($data,$tplname)
	{
		if (!$this->noview){ return false; }
		if ( $tplname !== false ){
			$this->tpname = $tplname;			
		}	
		$this->arData = $data;
	}

	/**
	 * Метод определение вьюшки и передачу в неё массива данных 
	 * @param $data - Массив данных передающихся во вьюшку
	 * @param $tplname - имя вьюшки если оно отличается от имени контроллера
	 * @return $this, ad_controller
 	*/

	public function view($data = array(),$tplname = false)
	{

		$this->_start($data,$tplname);
		ob_start(); 
		if (file_exists(AD::dir()."/viewer/".$this->tpname.".php")){
			include_once(AD::dir()."/viewer/".$this->tpname.".php");
		} else {
			include_once(AD::dir()."/viewer/default.php");			
		}
		$this->content = ob_get_contents();
		ob_end_clean();
		return $this;
	}


	/**
	 * Метод включаеет кеширования вьюшке 
	 * @param $name - идентификатор кеша
	 * @param $time - время актуальности кеша
	 * @return $this, ad_controller
 	*/

	public function cache($name = false,$time = false)
	{
		if (!$name){
			$name = $this->name;
		}

		$this->cache = new ad_cache('tpl_'.$name,'tplcache');
		if ($this->cache->get($time)){
			$this->content = $this->cache->out();	
		}
		return $this;
	}

	/**
	 * Метод перекодирует в json данные и определяет вьюшку аналог метода view()
	 * @param $data - Массив данных
	 * @param $tplname - имя вьюшки  если оно отличается от имени контроллера
	 * @return $this, ad_controller
 	*/

	public function json($data = array(),$tplname = false)
	{
		header('Content-Type: application/json');
		$this->_start($data,$tplname);
		$this->content = json_encode($this->arData);
		return $this;
	}

	/**
	 * Примитивный метод шаблонизитора, заменяте в шаблоне ключи на значения этих ключей в массиве данных по маске.
	 * @param $data - Массив данных
	 * @param $tplname - имя вьюшки  если оно отличается от имени контроллера
	 * @return $this, ad_controller
 	*/
 	
	public function template($data = array(),$tplname = false)
	{

		$this->_start($data,$tplname);

		if (!!$this->content !== false){ return $this; }

		$tp = ROOT.AD::config('templatepath').$this->tpname.".tpl";
		if(!file_exists($tp)){
			$tp = ROOT.'/'.$this->tpname;
		}

		$this->content = file_get_contents($tp);
		foreach ($this->arData as $key => $var) {
			$this->content = str_replace('{%'.$key.'%}', $var, $this->content);
		}
		return $this;

	}

	public function jscript($script)
	{
		$this->content = " <script>".$script."</script> ";
		return $this;
	}


	public function toUTF($string){
      	$enc=mb_detect_encoding($string); //узнаем кодировку
        $estring=iconv('windows-1251' , "UTF-8", $string); //перегоняем из cp в utf
        return isset($estring) ? $estring :  $string;
	}
	
}
?>