<?php

require_once CLASSES .  'users.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Controller for Users resource
 *
 * @namespace Exerciser\Resources
 * @uri /users
 */
class UsersResource extends MultiViewResource {

	/**
	 * Constructor for Users reasource
	 * @param array of configuration parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new Users($this->db);
	}

	/**
	 * Handle a GET request for this resource
	 * Returns a document containing a list of users
	 * @param Request request
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$this->model->location($request->uri);	
		$this->view = $this->get_view($request);
		try{
			$this->model->load();
			$response = new Response($request);
			$response->code = Response::OK;
			$response->addHeader('Content-type', $this->model->type($this->view));
			$response->body = $this->model->generateDocument($this->view);
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		return $response;
	}
	
	/**
	 * Handle a POST request for this resource
	 * Forwards request on to User
	 * @param Request request
	 * @return Response
	 */
	public function post($request){
		// forwards request on to User
		$user = new UserResource($this->parameters);
		return $user->post($request);		
	}    
}
?>
