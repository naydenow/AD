<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел
*/
class ad_table extends ad_safemysql
{
	private $arWhere = array();
	private $arField = array();
	private $arProps = array();
	private $tables = array();

	/* Добовляет значения выборки в массив выборки */
	public function where()
	{
		$arg = func_get_args();
		if (!empty($this->arWhere)){
			$arg[] = 'and';
		}
		$arg[2] = $this->_validate($arg[2]);
		$this->arWhere[] =  $arg;
		return $this;
	}
	//Добавляем искомые столбцы в массив
	public function fields()
	{
		$fields = func_get_args();
		if (is_array($fields)){
			$this->arField = array_merge($this->arField,$fields);
		}else {
			$this->arField[] =$fields;
		}
		return $this; 
	}

	//Добавляем значения в массив 
	public function props()
	{
		$props = func_get_args();
		if (is_array($props)){
			$this->arProps = array_merge($this->arProps,$props);
		}else {
			$this->arProps[] =$props;
		}
		return $this; 
	}

	//Добавляем имена таблиц в массив
	public function tables()
	{
		$table = func_get_args();
		if (is_array($table)){
			$this->tables = array_merge($this->tables,$table);
		}else {
			$this->tables[] =$table;
		}
		return $this; 
	}



	public function andWhere()
	{
		$arg = func_get_args();
		$arg[] = 'and';
		$arg[2] = $this->_validate($arg[2]);
		$this->arWhere[] = $arg;
		return $this;
	}
	public function orWhere()
	{
		$arg = func_get_args();
		$arg[] = 'or';
		$arg[2] = $this->_validate($arg[2]);
		$this->arWhere[] = $arg;
		return $this;
	}
	/* Для связи между таблицами без валидации */
	public function andConnectWhere()
	{
		$arg = func_get_args();
		$arg[] = 'and';
		$this->arWhere[] = $arg;
		return $this;
	}
	public function orConnectWhere()
	{
		$arg = func_get_args();
		$arg[] = 'or';
		$this->arWhere[] = $arg;
		return $this;
	}

	/* Собираем условия */
	private function _whereComplit()
	{
		if(empty($this->arWhere)) return '';
		$first  = array_shift($this->arWhere);
		$where = ' where '.$first[0].' '.$first[1].' '.$first[2] ;
		foreach ($this->arWhere as $wh) {
			$where .= ' '.$wh[3].'  '.$wh[0].' '.$wh[1].' '.$wh[2] ;	
		}
		return $where;

	}
	//Собираем все столбцы в строку
	private function _allFieldsComplite()
	{
		return implode(',', array_merge($this->fillable, $this->arField ));
	}
	//Собираем переданные сттолбцы в строку
	private function _pushFieldsComplite(){
		return implode(',', $this->arField );
	}

	//Собираем переданные значения в строку
	private function _pushPropsComplite(){
		// $this->arProps = array_map(function($v){
		// 	return $this->_validate($v);
		// }, $this->arProps);
		// return implode(",", $this->arProps );
		return "'".implode("' , '", $this->arProps )."'";
	}
	//Собираем таблицы в строку
	private function _compliteTables(){
		if (!is_array($this->table)){
			$this->table = array($this->table);
		}
		$this->tables = array_merge($this->tables,$this->table);
		return implode(',', $this->tables );
	}



	/* Собственно сам селект */
	public function get($n = 1)
	{
		$vi = ['getOne', 'getRow', 'getAll','getCol'];
		$query = "SELECT ".$this->_allFieldsComplite()." 
							FROM ".$this->_compliteTables().' 
							'.$this->_whereComplit();


		print_r($query);
		exit();
		return $this->{$vi[$n]}($query);
	}

	public function insert(){
		$query = "INSERT INTO ".$this->table.
							  " ( ".$this->_pushFieldsComplite()." ) ".
							  " VALUE (".
							  	$this->_pushPropsComplite().
							  ") ".$this->_whereComplit();

		return $this->rawQuery($query);
	}
}