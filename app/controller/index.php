<?php 
class index extends ad_controller {
	public function index_action(){ 
		$this->setTemplate();

		$coll = $this->collection_objects;
		$this->view((array)$coll::pagination())->render();
	}
	


	public function e404_action(){
		var_dump($this->collection_test());
	}

}

?>