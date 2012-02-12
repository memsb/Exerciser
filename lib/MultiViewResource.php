<?php

require_once '/usr/share/php5/tonic/lib/tonic.php';

class MultiViewResource extends Resource {

	protected $views = array();
	protected $parameters = array();
	protected $model;

	public function __construct($parameters){
		$this->parameters = $parameters;
	}

	/**
	* Defaults to xml
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

	protected function isSecured($username, $password) {
		if (
		isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $username &&
		isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW'] == $password
		) {
			return;
		}
		throw new ResponseException('Incorrect username and password', Response::UNAUTHORIZED);
	}
}

?>
