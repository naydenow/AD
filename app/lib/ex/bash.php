<?php 
namespace ex;
class bash {
	static private $data = array();
	static private $dataForm = array();


	static public function getNodeProcess(){
		$p =  self::exec("ps ax | grep 'node'");


		$list = [];
		foreach ($p as $st) {
			$v = preg_split('/node/', $st);

			$name =$v[1];
			$pid = explode('?',$v[0])[0];
		
			array_push($list, ['name' => $name,'pid' => $pid]) ;
		}
		
		return $list;
	}

	static public function getProcess(){
		return self::exec("ps ax");
	}

	static public function kill ($pid){
		return self::exec("kill -9 ".$pid);
	}

	static public function startNodeProcess($file){

		$logname = 'log/'.basename($file).".log";
		$file = escapeshellarg($file);

		return self::exec("node $file >$logname 2>&1 & echo $!");

	}

	static public function passthru($str)
	{
		return passthru($str);
	} 

	static	public function exec ($str){
		$data;
	//	echo($str);
		exec($str,$data);
		return $data; 
	}



}