<?php

require_once 'username.php';
require_once 'workouts.php';
require_once 'date.php';
require_once 'workout.php';

require_once 'xmlStats.php';

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
		$view = new xmlStats();
		$view->load();		

		$response = new Response($request);
		$response->code = Response::OK;
		$response->addHeader('Content-type', $view->type());
		$response->body = $request->method . " returns usage statistics: " . $view->toString();

		return $response;
	}


	/**
	* Handle a POST request for this resource
	* @param Request request
	* @return Response
	*/
	function post($request) {

		$response = new Response($request);

		/*
			on incomming workout XML data
			$data = request->body();
			$view = new xmlWorkouts();
			$list = $view->parse();
			workouts::add($list)
		*/
			
		$response->code = Response::OK;
		$response->addHeader('Content-type', 'text/xml');
		$response->addEtag($etag);
		$response->body = $request->method . " Creates a new user account";

		return $response;

	}    
}

?>
