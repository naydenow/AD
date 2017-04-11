<?php
namespace ad\np\;
class LOG {
	static public function set($message,$module = ''){
		file_put_contents($module.'log.log', print_r($message,true));
	}
}