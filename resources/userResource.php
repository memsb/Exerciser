<?php

require_once CLASSES .  'user.php';
require_once LIB .  'MultiViewResource.php';

/**
 * @author Martin Buckley - MBuckley@gmail.com
 * Controller for User resource
 *
 * @namespace Exerciser\Resources
 * @uri /users/([A-Za-z0-9_]+)
 */
class UserResource extends MultiViewResource {

	/**
	 * Constructor for User resource
	 * @param array of configuration parameters
	 */
	public function __construct($parameters){
		parent::__construct($parameters);
		$this->model = new User($this->db);
			
	}

	/**
	 * Handle a GET request for this resource
	 * Returns a document containing information on a specified user
	 * @param Request request
	 * @return Response
	 */
	public function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];
		$this->view = $this->get_view($request);
		try{
			$this->model->setLocation($request->uri);
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
	 * Handle a PUT request for this resource
	 * Updates the specified users details using the information from the document provided
	 * Requires users autherisation via Basic Auth and a valid API key
	 * @param Request request
	 * @return Response
	 */
	public function put($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];
		$this->view = $this->get_view($request);
		try{
			$this->model->setLocation($request->uri);
			$this->model->load($ID);
			$this->isSecured($this->model->user_id, $this->model->api_key);
			$this->model->parse($request->data, $this->view);
			$this->model->update();		
	
			$response->code = Response::CREATED;
			$response->addHeader('Location', $this->model->location());
		}catch (ResponseException $e){
			throw $e;
		}catch (UnexpectedValueException $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		return $response;  
	}

	/**
	 * Handle a POST request for this resource
	 * Creates a new site user using the details provided
	 * Returns a document containing the users ID number and API key for use in future autherisation
	 * @param Request request
	 * @return Response
	 */
	public function post($request){
		$response = new Response($request);
		$this->view = $this->get_view($request);
		try{	
			$this->model->setLocation($request->uri);	
			$this->model->parse($request->data, $this->view);
			$this->model->create();			
			$response->code = Response::CREATED;
			$response->addHeader('Content-type', $this->model->type($this->view));
			$response->addHeader('Location', $this->model->location());
			$response->body = $this->model->generateNewUserDocument($this->view);
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
			$response->body = $e->getMessage();
		}
		return $response;
	}
	
	/**
	 * Handle a DELETE request for this resource
	 * Requires users autherisation via Basic Auth and a valid API key
	 * @param Request request
	 * @return Response
	 */
	public function delete($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];	

		try{
			$this->model->load($ID);
			$this->isSecured($this->model->user_id, $this->model->api_key);
			$this->model->delete();

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
