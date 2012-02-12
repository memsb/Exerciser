<?php

require_once dirname(__FILE__) . '/../classes/users.php';
require_once dirname(__FILE__) . '/../lib/MultiViewResource.php';

/**
 * @namespace Tonic\Exerciser\Users
 * @uri /users
 */
class UsersResource extends MultiViewResource {

	protected $views = array(	'xml' => 'xmlUsers', 
					'json' => 'jsonUsers', 
					'yaml' => 'yamlUsers'
				);

	function __construct($parameters){
		  parent::__construct($parameters);
	}
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		$view = $this->get_view($request);
		$model = new Users($request->uri);		

		try{
			$model->load();
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
	* Handle a POST request for this resource
	* @param Request request
	* @return Response
	*/
	function post($request){
		$user = new UserResource($this->parameters);
		return $user->post($request);		
	}    
}
?>
