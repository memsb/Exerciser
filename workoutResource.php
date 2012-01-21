<?php

require_once('workout.php');

/**
 * @namespace Tonic\Exerciser\Username\Workouts\Workout
 * @uri /([A-Za-z0-9_]+)/workouts/([0-9]+)
 */
class WorkoutResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		$model = new Workout();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$model->load($ID);

		$view = new XMLWorkout();
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

		$workout = new Workout();
		$view = new xmlWorkout();

		parse_str(urldecode($request->data), $data);

		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$workout->load($ID);
		$view->parse($data['user'], $workout);
		$workout->update();		

		$response->code = Response::CREATED;
		$response->body = $workout->location();

		return $response;
	}

	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	function delete($request) {
		$response = new Response($request);

		$workout = new Workout();

		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$workout->load($ID);
		$workout->delete();		

		$response->code = Response::CREATED;

		return $response;
	}
    
}
?>
