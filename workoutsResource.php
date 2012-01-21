<?php

require_once('workouts.php');
require_once('workout.php');

/**
 * @namespace Tonic\Exerciser\Username\Workouts
 * @uri /([A-Za-z0-9_]+)/workouts/
 */
class WorkoutsResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		$model = new Workouts();
		$bits = explode('/', $request->uri);
		$ID = $bits[1];
		$model->load($ID);

		$view = new XMLWorkouts();
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

		$user = new Workout();
		$view = new xmlWorkout();
		parse_str(urldecode($request->data), $data);;
		$view->parse($data['user'], $user);
		$user->create();		
	
		$response->code = Response::CREATED;
		$response->addHeader('Content-type', $view->type());
		$response->body = '';

		return $response;
	}


	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	function delete($request) {
		$response = new Response($request);

		$model = new Workouts();
		$bits = explode('/', $request->uri);
		$ID = $bits[1];
		$model->load($ID);
		$model->delete();

		$response = new Response($request);
		$response->code = Response::OK; 
		$response->body = $ID;

		return $response;        
	}
}
?>
