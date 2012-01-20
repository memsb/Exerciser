<?php


/**
 * @namespace Tonic\Exerciser\Username\Workouts\Workout\Date
 * @uri /([A-Za-z0-9_]+)/workouts/([0-9]{2}-[0-9]{2}-[0-9]{4})
 */
class DateResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		/* 
		get request details
		get user by username
		$user = User::find(username)
		get workouts by date and user id
		$list = workouts::get($users->id, date)
		$view = new xmlWorkouts();
		$view->generate($list); 
		$view->toString();	

		*/
		$response->code = Response::OK;
		$response->addHeader('Content-type', 'text/plain');
		$response->addEtag($etag);
		$response->body = $request->method . " Date ";

		return $response;

	}

	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	function delete($request) {
		$response = new Response($request);
		$response->code = Response::OK;
		$response->addHeader('Content-type', 'text/plain');
		$response->addEtag($etag);
		$response->body = $request->method . "Date";
		return $response;

	}    
}
?>
