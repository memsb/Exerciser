<?php

require_once CLASSES .  'workout.php';
require_once CLASSES .  'user.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Controller for Workout resource
 *
 * @namespace Exerciser\Resources
 * @uri /workouts/([0-9]+)
 */
class WorkoutResource extends MultiViewResource {

	/**
	 * Constructor for Workout reasource
	 * @param array of configuration parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new Workout($this->db);
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
	 * Returns a document containing workout information
	 * @param Request request
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];
		$this->model->setLocation($request->uri);
		$this->view = $this->get_view($request);
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
	 * Creats a new wrkout for the specified user using the details in the document provided
	 * Requires users autherisation via Basic Auth and a valid API key
	 * @param Request request
	 * @return Response
	 */
	public function post($request){
		$response = new Response($request);
		$ID = $this->parameters[0];
		try{
			$this->user->load($this->model->user_id);
			$this->isSecured($this->user->user_id, $this->user->api_key);

			$this->model->parse($request->data, $this->view);
			$this->model->create();		

			$response->code = Response::CREATED;
			$response->addHeader('Location', $this->model->location());
		}catch (ResponseException $e){
			throw $e;
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
		}
		return $response;
	}

	/**
	 * Handle a PUT request for this resource
	 * Updates the details of the specified workout using the details contained within the passed document
	 * Requires users autherisation via Basic Auth and a valid API key
	 * @param Request request
	 * @return Response
	 */
	public function put($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];	

		try{
			$this->model->load($ID);
			
			$this->user->load($this->model->user_id);
			$this->isSecured($this->user->user_id, $this->user->api_key);

			$this->model->parse($request->data, $this->view);
			$this->model->update();		
			
			$response->code = Response::OK;
			$response->addHeader('Location', $this->model->location());
		}catch (ResponseException $e){
			throw $e;
		}catch (UnexpectedValueException $e){
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		return $response;
	}

	/**
	 * Handle a DELETE request for this resource
	 * Deletes a specified workout
	 * Requires users autherisation via Basic Auth and a valid API key
	 * @param Request request
	 * @return Response
	 */
	public function delete($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];

		try{
			$this->model->load($ID);
			$this->user->load($this->model->user_id);
			$this->isSecured($this->user->user_id, $this->user->api_key);

			$this->model->delete();
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
