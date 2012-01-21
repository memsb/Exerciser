<?php


require_once('user.php');

/**
 * @namespace Tonic\Exerciser\Username
 * @uri /([A-Za-z0-9_]+)
 */
class UsernameResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		$model = new User();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$model->load($ID);
		
		$view = new XMLUser();
		$view->generateDocument($model);

		$response = new Response($request);
		$response->code = Response::OK;
		$response->addHeader('Content-type', $view->type());
		$response->body = $view->toString();     

		return $response;
	}


	/**
	* Handle a PUT request for this resource
	* @param Request request
	* @return Response
	*/
	function put($request) {
		$response = new Response($request);

		$model = new User();
		$view = new xmlUser();

		parse_str(urldecode($request->data), $data);

		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$user->load($ID);
		$view->parse($data['user'], $model);
		$model->update();		
	
		$response->code = Response::CREATED;
		$response->body = $model->location();

		return $response;  
	}


	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	function delete($request) {
		$response = new Response($request);

		$model = new User();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$model->load($ID);
		$model->delete();

		$response = new Response($request);
		$response->code = Response::OK; 
		$response->body = $model->username;

		return $response;
	}
    
}
?>
