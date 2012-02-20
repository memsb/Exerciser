<?php

require_once CLASSES .  'activity.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Resource controller for an activity.
 *
 * @namespace Exerciser\Resources
 * @uri /activities/([0-9]+)/
 */
class ActivityResource extends MultiViewResource {

	/**
	 * Creates an default instance of model
	 * @param array parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new Activity($this->db);
	}
    
	/**
	 * Handle a GET request for this resource
	 * returns a document containing information on an activity
	 * @param Request request object containing parameters
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];
		
		$this->view = $this->get_view($request);
		$this->model->setLocation($request->uri);

		try{
			$this->model->load($ID);						
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
			return $response;
		}	     
		
		$response->code = Response::OK;
		$response->addHeader('Content-type', $this->model->type($this->view));
		$response->body = $this->model->generateDocument($this->view);
		return $response;
	}
}
?>
