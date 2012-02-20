<?php

require_once CLASSES .  'stats.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Controller for Stats Resource.
 *
 * @namespace Exerciser\Resources
 * @uri /
 */
class ExerciserResource extends MultiViewResource {

	/**
	 * Constructor for Stats reasource
	 * @param array of configuration parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new Stats($this->db);
	}
    
	/**
	 * Handle a GET request for this resource
	 * Returns a document containing service usage statistics
	 * @param Request request
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$this->model->setLocation($request->uri);
		$this->view = $this->get_view($request);
		try{
			$this->model->load();			
			$response->code = Response::OK;
			$response->addHeader('Content-type', $this->model->type($this->view));
			$response->body = $this->model->generateDocument($this->view);
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		return $response;
	}  
}

?>
