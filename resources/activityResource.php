<?php

require_once dirname(__FILE__) . '/../classes/activity.php';
require_once dirname(__FILE__) . '/../lib/MultiViewResource.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Resource controller for an activity.
 *
 * @namespace Tonic\Exerciser\Activities\Activity
 * @uri /activities/([0-9]+)/
 */
class ActivityResource extends MultiViewResource {

	protected $views = array(	'xml' => 'xmlActivity', 
					'json' => 'jsonActivity', 
					'yaml' => 'yamlActivity'
				);

	/**
	* Creates an instance of the resource
	* @param array parameters
	*/
	function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new Activity();
	}

	/**
	* Sets the model to be controlled
	* @param Activity model
	*/
	function setModel($model){
		$this->model = $model;
	}
    
	/**
	* Handle a GET request for this resource
	* @param Request request object containing parameters
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];

		$view = $this->get_view($request);		
		$this->model->location($request->uri);

		try{
			$this->model->load($ID);						
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
			return $response;
		}
	     
		$view->generateDocument($this->model);
		$response->code = Response::OK;
		$response->addHeader('Content-type', $view->type());
		$response->body = $view->toString();
		return $response;
	}
}
?>
