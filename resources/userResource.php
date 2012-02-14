<?php

require_once dirname(__FILE__) . '/../classes/user.php';
require_once dirname(__FILE__) . '/../lib/MultiViewResource.php';

/**
 * @namespace Tonic\Exerciser\Users\Username
 * @uri /users/([A-Za-z0-9_]+)
 */
class UserResource extends MultiViewResource {

	protected $views = array(	'xml' => 'xmlUser', 
					'json' => 'jsonUser', 
					'yaml' => 'yamlUser'
				);

	public function __construct($parameters){
		  parent::__construct($parameters);
	}
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	public function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];

		$view = $this->get_view($request);
		$model = new User($request->uri);		

		try{
			$model->load($ID);
			$view->generateDocument($model);

			$response = new Response($request);
			$response->code = Response::OK;
			$response->addHeader('Content-type', $view->type());
			$response->body = $view->toString();
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}     

		return $response;
	}


	/**
	* Handle a PUT request for this resource
	* @param Request request
	* @return Response
	*/
	public function put($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];

		$view = $this->get_view($request);
		$model = new User($request->uri);

		try{
			$model->load($ID);
			$this->isSecured($model->user_id, $model->password);
			$view->parse($request->data, $model);
			$model->update();		
	
			$response->code = Response::CREATED;
			$response->addHeader('Location', $model->location());
		}catch (UnexpectedValueException $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		return $response;  
	}

	public function post($request){
		$response = new Response($request);
		$view = $this->get_view($request);
		$model = new User($request->uri);

		try{			
			$view->parse($request->data, $model);
			$model->create();
			$view->generateNewUserDocument($model);
			$response->code = Response::CREATED;
			$response->addHeader('Content-type', $view->type());
			$response->addHeader('Location', $model->location());
			$response->body = $view->toString();
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
			$response->body = $e->getMessage();
		}		

		return $response;
	}

	
	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	public function delete($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];

		$model = new User($request->uri);		

		try{
			$model->load($ID);
			$this->isSecured($model->user_id, $model->password);
			$model->delete();

			$response = new Response($request);
			$response->code = Response::NOCONTENT;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		} 

		return $response;
	}
    
}
?>
