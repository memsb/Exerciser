<?php

require_once LIB . 'view.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	CRUD - Create Retrive Update Delete. A basis for classes to represent system objects
 *	@namespace Exerciser
 */
class CRUD {

	protected $uri = '';
	protected $db = null;
	protected $type = '';

	public function __construct($db){		
		if( $db == NULL )
			throw new BadMethodCallException('Database not specified.');
		$this->db = $db;
		$this->db->connect();
	}

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
	 * @param mixed optional identifier
	 */
	public function load($ID=NULL){
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
	 * Deletes the object from the database
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

	/**
	 * Gets the mime type
	 * @param View the view to combine the type with
	 * @return String the mime type
	 */
	public function type($view){
		if( ! $view )
			return '';
		return $view->type() . $this->type;
	}

	/**
	 * Parses a document using the specified view
	 * @param String the document string
	 * @param View the view
	 */
	public function parse($data_str, $view){
		throw new BadMethodCallException('Function not implemented.');
	}

	/**
	 * Generates a document from the model and the selected view
	 * @param View the view
	 * @return String the generated document
	 */
	public function generateDocument($view){
		if( ! $view )
			return '';
		$view->startDocument();
		$this->addProperties($view);
		return $view->toString();
	}
}
?>
