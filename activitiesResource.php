<?php

require_once 'activities.php';

/**
 * @namespace Tonic\Exerciser\Activities
 * @uri /activities/
 */
class ActivitiesResource extends Resource {
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);

		$model = new Activities();
		$model->load();

		$view = new XMLActivities();
		$view->generateDocument($model);

		$response = new Response($request);
		$response->code = Response::OK;
		$response->addHeader('Content-type', $view->type());
		$response->body = $view->toString();     

		return $response;
	}
}
?>
