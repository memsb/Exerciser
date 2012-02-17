<?php

require_once dirname(__FILE__) . '/../lib/view.php';

/**
*	@author Martin Buckley - MBuckley@gmail.com
*	CRUD - Create Retrive Update Delete. A basis for classes to represent system objects
*/
class CRUD {

	protected $uri = '';
	protected $db = null;

	/**
	* Setter magic method
	* @param String attribute name
	* @param Mixed attribute value
	*/
	public function __set($name, $value){
		$this->$name = $value;
	}

	/**
	* Getter magic method
	* @param String attribute name
	* @return Mixed the attribute
	*/
	public function __get($name){
		return $this->$name;
	}

	/**
	* Loads the objects attributes from the database
	*/
	public function load($ID){
		throw new BadMethodCallException('Function not implemented.');
	}

	/**
	* Creates a new object in the database
	*/
	public function create(){
		throw new BadMethodCallException('Function not implemented.');
	}

	/**
	* Updates the object in the database
	*/
	public function update(){
		throw new BadMethodCallException('Function not implemented.');
	}

	/**
	* Deteles the object from the database
	*/
	public function delete(){
		throw new BadMethodCallException('Function not implemented.');
	}

	/**
	* Sets the uri of the object
	* @param String the location uri
	*/
	public function setLocation($uri){
		$this->uri = $uri;
	}

	/**
	* Gets the uri of the object
	* @return String the location uri
	*/
	public function location(){
		return $this->uri;
	}

	public function parse($data_str, $view){
		throw new BadMethodCallException('Function not implemented.');
	}

	/**
	*
	*
	*/
	public function generateDocument($view){
		$view->startDocument();
		$this->addProperties($view);
		return $view->toString();
	}
}
?>
