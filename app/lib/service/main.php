<?php

/* adapter service */

namespace service;

class service {
	use \ad\atrait\dic;
	function __construct($name,$arg){
		$this->name = $name;
		$this->arg = $arg;
	}

	public function start(){

	}

	public function stop($cache){

	}

}