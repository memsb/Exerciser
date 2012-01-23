<?php

require_once dirname(__FILE__) . '/../classes/workout.php';

/**
 * @namespace Tonic\Exerciser\Username\Workouts\Workout
 * @uri /([A-Za-z0-9_]+)/workouts/([0-9]+)
 */
class WorkoutResource extends Resource {

	private $views = array(	'xml' => 'xmlWorkout', 
				'json' => 'jsonWorkout', 
				'yaml' => 'yamlWorkout'
				);

	function get_view($request){		
		$request->mimetypes['yaml'] = 'text/x-yaml';
		$format = $request->mostAcceptable(array_keys($this->views));
		if(!$format){
			throw new Exception("Format not acceptable");
		}
		return new $this->views[$format];
	}
    
	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);
		
		$view = $this->get_view($request);

		$model = new Workout();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];

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

	function post($request){
		$response = new Response($request);
		try{
			$view = $this->get_view($request);
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
			return $response;
		}

		$workout = new Workout();

		$view->parse($request->data, $workout);
		$workout->create();		

		$response->code = Response::CREATED;
		$response->body = $request->uri . $workout->location();
		//
		//UNAUTHORIZED
		return $response;
	}

	/**
	* Handle a PUT request for this resource
	* @param Request request
	* @return Response
	*/
	function put($request) {
		$response = new Response($request);
		try{
			$view = $this->get_view($request);
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
			return $response;
		}

		$workout = new Workout();

		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];

		try{
			$workout->load($ID);
			$view->parse($request->data, $workout);
			$workout->update();		

			$response->code = Response::OK;
			$response->body = $request->uri;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		//
		//UNAUTHORIZED
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

		try{
			$workout->load($ID);
			$workout->delete();		

			$response->code = Response::NOCONTENT;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}

		//Response::UNAUTHORIZED is also needed

		return $response;
	}
    
}
?>
