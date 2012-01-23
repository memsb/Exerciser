<?php

require_once dirname(__FILE__) . '/../classes/workouts.php';

/**
 * @namespace Tonic\Exerciser\Username\Workouts
 * @uri /([A-Za-z0-9_]+)/workouts/
 */
class WorkoutsResource extends Resource {
    
	private $views = array(	'xml' => 'xmlWorkouts', 
				'json' => 'jsonWorkouts', 
				'yaml' => 'yamlWorkouts'
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

		$model = new Workouts();
		$bits = explode('/', $request->uri);
		$ID = $bits[1];

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
    

	/**
	* Handle a POST request for this resource
	* @param Request request
	* @return Response
	*/
	function post($request) {
		// forwards request on to workout
		$test = new WorkoutResource(null);
		$response = $test->post($request);
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

		try{
			$model->load($ID);
			$model->delete();

			$response = new Response($request);
			$response->code = Response::NOCONTENT; 
			$response->body = $ID;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}

		return $response;        
	}
}
?>
