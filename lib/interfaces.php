<?php

/**
*	@author Martin Buckley - MBuckley@gmail.com
*	View class provides a basis for specific views.
*/
class View {

	protected $type = '';
	protected $data;
	protected $writer;

	/**
	* Parses the supplied document into the model provided
	* @param Mixed the document string
	* @param Mixed an instance of the model
	*/
	public function parse($data, $model){
		throw new BadMethodCallException('This object cannot be parsed');
	}

	/**
	* Creates an internally stored document from the model
	* @param Mixed an instance of the model
	*/
	public function generateDocument($model){}
	
	/**
	* Appends the elements to the specified data using the specified model
	* @param Mixed an instance of the model
	* @param Mixed the data
	*/
	public function addElements($model, &$writer){}

	/**
	* Gets the views mime type
	* @return String indicating the views mime type
	*/
	public function type(){
		return $this->type;
	}

	/**
	* Gets the string representing the internal document
	* @return String the document string
	*/
	public function toString(){
		return '';
	}
}

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
}
?>
