<?php 
class ad_request {
	

	public $payload = [],
		   $query = [],
		   $headers = [],
		   $method,
		   $url,
		   $params = [];		

	function __construct($get = [], $post = []){
		$this->payload = $post;
		$this->query   = $get;
		$this->headers = getallheaders();
		$this->method  = $_SERVER['REQUEST_METHOD'];
		$this->url = $_SERVER['REQUEST_URI'];
	}
}