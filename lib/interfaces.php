<?php
interface View {

	public function parse($data, $model);

	public function generateDocument($model);
	
	public function addElements($model, &$writer);

	public function type();
 
	public function toString();
}

abstract class CRUD {

	public function __set($name, $value){
		$this->$name = $value;
	}

	public function __get($name){
		return $this->$name;
	}

	public function load($ID){
		throw new Exception('Function not implemented.');
	}

	public function create(){
		throw new Exception('Function not implemented.');
	}

	public function update(){
		throw new Exception('Function not implemented.');
	}

	public function delete(){
		throw new Exception('Function not implemented.');
	}

	public function location(){
		throw new Exception('Function not implemented.');
	}
}
?>
