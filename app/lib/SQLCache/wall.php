<?php
 namespace SQLCache;

class wall {
	use \ad\atrait\dic;
	function __construct($userid){
		$this->userid = $userid;
	}
	public function get(){
		$data = $this->table_wallcache->get($this->userid);
		if(!empty($data))
			return unserialize($data);
		else 
			return false;
	}
	public function set($cache){
		$this->table_wallcache->set($this->userid,serialize($cache));
	}

}