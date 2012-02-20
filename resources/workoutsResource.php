<?php

require_once CLASSES .  'workouts.php';
require_once CLASSES .  'user.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Controller for Workouts resource
 *
 * @namespace Exerciser\Resources
 * @uri /users/([A-Za-z0-9_]+)/workouts/
 */
class WorkoutsResource extends MultiViewResource {

	/**
	 * Constructor for Workouts reasource
	 * @param array of configuration parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new Workouts($this->db);
		$this->user = new User($this->db);
	}

	/**
	 * Sets the user model
	 * @param User model
	 */
	public function setUser($user){
		$this->user = $user;
	} 

	/**
	 * Handle a GET request for this resource
	 * Returns a document containing information on a specified users workouts
	 * @param Request request
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];
		$this->model->setLocation($request->uri);
		$this->view = $this->get_view($request);

		if ( isset($_GET['date']) )
			$this->model->setDate($_GET['date']);

		if ( isset($_GET['from']) && isset($_GET['to']) )
			$this->model->setPeriod($_GET['from'], $_GET['to']);

		if ( isset($_GET['activity']) )
			$this->model->setActivity($_GET['activity']);		

		try{
			$this->model->load($ID);		

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
	 * Forwards request on to workout
	 * @param Request request
	 * @return Response
	 */
	public function post($request) {
		// forwards request on to workout
		$workout = new WorkoutResource($this->parameters);
		return $workout->post($request);
	}


	/**
	 * Handle a DELETE request for this resource
	 * Deletes the specified users workouts
	 * @param Request request
	 * @return Response
	 */
	public function delete($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];	

		try{
			$this->user->load($this->model->user_id);
			$this->isSecured($this->user->user_id, $this->user->api_key);

			$this->model->delete($ID);

			$response = new Response($request);
			$response->code = Response::NOCONTENT;
		}catch (ResponseException $e){
			throw $e;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}

		return $response;        
	}
}
?>
