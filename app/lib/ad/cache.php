<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел

Класс кеширования данных, можно кешировать как строки так и php объекты.
При создании экземпляра класса в него передаётся идентификатор ( строка ) и тип кеширования, по умолчанию phpcache, 
Так же создаётся файл блокировки, как только данные будут за кешированы файл блокировки удалится.
Пока существует файл блокировки все скрипты находятся в режими ожидания.

*/
class ad_cache 
{
	private $lock = false;
	private $mycache = false;
	private $cacheconf= array('html' 		 => 	array('in'=>'notserialize','out'=>'notserialize','h'=>'.html'),
							  'serialize' 	 => 	array('in'=>'serialize','out'=>'unserialize','h'=>'.c'),
							  'json'		 => 	array('in'=>'json_encode','out'=>'json_decode','h'=>'.json'));


	function __construct($name,$co = false)
	{
		if (!$co ){
			$co = 'phpcache';
		}
		$this->conf = AD::config($co);
		$this->name = $name;
		$this->cc = $this->cacheconf[$this->conf['metod']];



		if ( ( $srp = strrpos($this->name, '/') )  > 0 ){
			$this->conf['path'] .= substr($this->name, 0,$srp);
			if (!is_dir(ROOT.$this->conf['path'])){
				mkdir(ROOT.$this->conf['path'],0777,true);
			}
			$this->name = substr($this->name,$srp);
		} 	


		$this->file = ROOT.$this->conf['path'].$this->name.$this->cc['h'];
	}

	/* Ф-ия возвращает кеш */
	public function out()
	{
		return $this->content;
	}

	/**
	 * Метод проверяет существует ли кеш по заданным параметрам, если кеш существует то он записывается  в content
	 * @param $time - допустимое6 время существоввания кеша
	 * @return true || false
 	*/
	public function get($time = false)
	{

		/* Создаём файл блокировки */		
		if (!file_exists($this->file)){				
			file_put_contents(ROOT.$this->conf['path'].$this->name.$this->cc['h'].'.lock', '');
			$this->mycache = true;
			$this->lock = true;
		}

		$t = 0;
		/*  Если создан файл блокировки, значить в данный момент выполняется создание кеша,
			нужно дождаться пока создаться файл кеша, для этого проверяем его наличие  каждую секунду */		
		while($this->lock && !$this->mycache){
			usleep(1000);
			$t++;
			if (file_exists($this->file)){	
				//Файл создан выходим из цикла
				$this->lock = false;
			}
			if($t > 5){
				//Превышен лимит ожидания, выходим из цикла
				return false;
			}
		} 
		if (!$time && intval( $this->conf['time'] ) > 0 ){
			$time = $this->conf['time'];
		}
		//Проверяем наличие файла кеша
		if (file_exists($this->file)){
			//Проверяем кеш на актуальность
			if ((time() - $time) < filemtime($this->file)){ 
				$this->content =  $this->cc['out']( file_get_contents($this->file) );
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}



	/**
	 * Метод сохранения объекта  или строки в кеш
	 * @param $data - php обьект или строка
	 * @return ad_cache
 	*/

	public function save($data)
	{
		if(is_callable($data)){
			$this->content = $data();
		} else {
			$this->content = $data;
		}

		if ($this->lock && !$this->mycache) return $this;
		$cahedata = $this->cc['in']($this->content);

		file_put_contents($this->file, $cahedata);
		@unlink($this->file = ROOT.$this->conf['path'].$this->name.$this->cc['h'].'.lock');
		return $this;

	}
	/* Удаляем кеш текущего экземпляра */
	public function remove(){
			unlink($this->file);
	}
}

/* Функция загушка */
function notserialize($r)
{
	return $r;
}