<?php

require_once '/usr/share/php5/tonic/lib/tonic.php';
require_once LIB . 'DBA.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Allows Resources derived from this class to handle multiple document types.
 * 	Currently handled are: XML, JSON, YAML
 * 	Default format is XML
 *	@namespace Exerciser
 */
class MultiViewResource extends Resource {

	protected $views = array(	'xml' => 'XML', 
					'json' => 'JSON', 
					'yaml' => 'YAML'
				);
	protected $parameters = array();
	protected $model;
	protected $view;
	protected $db;

	/**
	 * Constructor for all multi format resources
	 * Creates a new instance of the Database
	 */
	public function __construct($parameters){
		$this->parameters = $parameters;
		$this->db = new Database();
		try{
			$this->db->connect();
		}catch(Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * Sets the model to be controlled
	 * @param Mixed model
	 */
	public function setModel($model){
		$this->model = $model;
	} 

	/**
	 * Checks the parameters passed to the resource against a mime times list to select the apropriate view to handle the document type requested
	 * Defaults to xml
	 * @param Request instance
	 * @return View to handle the documents
	 */
	public function get_view($request){			
		$request->mimetypes['yaml'] = 'text/x-yaml';
		$request->accept[] = array_keys($this->views);
		$format = $request->mostAcceptable(array_keys($this->views));
		$view = $this->views[$format];
		if( !class_exists($view) )
			throw new Exception('No such view class ' . $view);
		return new $view;
	}

	/**
	* Checks for user autherisation using BAsic Authentication
	* @param String the username
	* @param String the password
	*/
	protected function isSecured($username, $password) {
		if( 	isset($_SERVER['PHP_AUTH_USER']) && 
			isset($_SERVER['PHP_AUTH_PW']) &&
			strcmp($_SERVER['PHP_AUTH_USER'], $username) == 0 &&
		 	strcmp($_SERVER['PHP_AUTH_PW'], $password) == 0 )
			return;
		throw new ResponseException('Incorrect username and password', Response::UNAUTHORIZED);
	}
}

?>
