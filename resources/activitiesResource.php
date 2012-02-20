<?php

require_once CLASSES .  'activities.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Controller for Activities.
 *
 * @namespace Exerciser\Resources
 * @uri /activities/
 */
class ActivitiesResource extends MultiViewResource {

	/**
	 * Constructor creates a new default instance of a model
	 * @param array or configuration parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);		
		$this->model = new Activities($this->db);
	}

	/**
	 * Handle a GET request for this resource
	 * Returns a document containing a list of all stored activities
	 * @param Request request
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$this->view = $this->get_view($request);
		$this->model->setLocation($request->uri);

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
