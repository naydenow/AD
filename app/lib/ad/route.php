<?php 
class ad_route {
	
	private $method, 
			$url,
			$param ,
			$fn;

	function __construct($method, $url, $fn){
		$this->method = $method;
		$this->url = $url;
		$this->fn = $fn;

		$param = [];

		$this->purl = preg_replace_callback('/{(.*?)}/i',function($c) use(&$param) {
			$param[] = $c[1];
			return '(.*)';
		},$url,6);

		$this->purl = '/'.preg_replace('/\//', '\/', $this->purl ).'/i';
		$this->param = $param;
	}

	public function test($request){	
		if ($request->method === $this->method){
			if ($this->parseUrl($request)){
				return $this->play($request);
			}
		}

		return false;;
	}

	private function parseUrl(&$request){

		if (preg_match($this->purl,$request->url,$matches)){
			$params = [];

			foreach ($this->param  as $index => $value) {
				$params[$value] = $matches[$index+1];
			}

			$request->params = $params;

			return true;
		}

		return false;
	}

	public function play($request){
		$fn = $this->fn;
		$fn($request, new ad_controller($this->url,''));

		return true;
	}

}