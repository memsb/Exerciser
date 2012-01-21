<?php

require_once 'includes.php';

require_once 'stats.php';
require_once 'user.php';

/**
 * @namespace Tonic\Exerciser
 * @uri /
 */
class ExerciserResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		// load things from the model, then pass to the view for wrapping
		$model = new Stats();
		$model->load();
		
		$view = new XMLStats();
		$view->generateDocument($model);

		$response = new Response($request);
		$response->code = Response::OK;
		$response->addHeader('Content-type', $view->type());
		$response->body = $view->toString();
		return $response;
	}


	/**
	* Handle a POST request for this resource
	* @param Request request
	* @return Response
	*/
	function post($request) {
		$response = new Response($request);

		$model = new User();
		$view = new xmlUser();
		parse_str(urldecode($request->data), $data);
		$view->parse($data['user'], $model);
		$model->create();
			
		$response->code = Response::CREATED;
		$response->addHeader('Content-type', $view->type());
		$response->body = $model->location();

		return $response;
	}    
}

?>
