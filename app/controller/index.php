<?php 
class index extends ad_controller {
	public function index_action(){ 
		$this->setTemplate();
		$this->view([])->render();
	}
	

	public function e404_action(){
		
	}

}

?>