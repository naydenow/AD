<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел

С этом классе реализорвана Dependency Injection Container, при попытке вызвать не существующим метод, 
создайться или берётся уже готовый экземпляр обьекта и возвращается.
*/

namespace ad\atrait;

Trait dic
{
	public  function __call($name,$arguments = array())
	{
		return \AD::DI($name,$arguments);
	}

	public function __get($n){
		return \AD::DI($n);
	}
}
