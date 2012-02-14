<?php

require_once dirname(__FILE__) . '/../classes/workouts.php';
require_once dirname(__FILE__) . '/../lib/MultiViewResource.php';

/**
 * @namespace Tonic\Exerciser\Users\Username\Workouts
 * @uri /users/([A-Za-z0-9_]+)/workouts/
 */
class WorkoutsResource extends MultiViewResource {
    
	protected $views = array(	'xml' => 'xmlWorkouts', 
					'json' => 'jsonWorkouts', 
					'yaml' => 'yamlWorkouts'
				);

	function __construct($parameters){
		  parent::__construct($parameters);
	}

	/**
	* Handle a GET request for this resource
	* @param Request request
	* @return Response
	*/
	function get($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];

		$view = $this->get_view($request);
		$model = new Workouts($request->uri);

		if ( isset($_GET['date']) )
			$model->setDate($_GET['date']);

		if ( isset($_GET['from']) && isset($_GET['to']) )
			$model->setPeriod($_GET['from'], $_GET['to']);

		if ( isset($_GET['activity']) )
			$model->setActivity($_GET['activity']);		

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
		$test = new WorkoutResource($this->parameters);
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
		$ID = $this->parameters[0];
	
		$model = new Workouts($request->uri);		

		try{
			$model->load($ID);
			$model->delete();

			$response = new Response($request);
			$response->code = Response::NOCONTENT;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}

		return $response;        
	}
}
?>
