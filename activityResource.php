<?php

require_once 'activity.php';

/**
 * @namespace Tonic\Exerciser\Activities\Activity
 * @uri /activities/([0-9]+)/
 */
class ActivityResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		$model = new Activity();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];
		$model->load($ID);

		$view = new XMLActivity();
		$view->generateDocument($model);

		$response = new Response($request);
		$response->code = Response::OK;
		$response->addHeader('Content-type', $view->type());
		$response->body = $view->toString();     

		return $response;
	}
}
?>
