<?php

require_once dirname(__FILE__) . '/../classes/workout.php';
require_once dirname(__FILE__) . '/../classes/user.php';
require_once dirname(__FILE__) . '/../lib/MultiViewResource.php';

/**
 * @namespace Tonic\Exerciser\Workouts\Workout
 * @uri /workouts/([0-9]+)
 */
class WorkoutResource extends MultiViewResource {

	protected $views = array(	'xml' => 'xmlWorkout', 
					'json' => 'jsonWorkout', 
					'yaml' => 'yamlWorkout'
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
		$model = new Workout($request->uri);		

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
		$ID = $this->parameters[0];
		
		$view = $this->get_view($request);
		$model = new Workout($request->uri);

		try{
			$user = new User('');
			$user->load($model->user_id);
			$this->isSecured($user->user_id, $user->password);

			$view->parse($request->data, $model);
			$model->create();		

			$response->code = Response::CREATED;
			$response->addHeader('Location', $model->location());
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
		}
		return $response;
	}

	/**
	* Handle a PUT request for this resource
	* @param Request request
	* @return Response
	*/
	function put($request) {
		$response = new Response($request);
		$ID = $this->parameters[0];
		
		$model = new Workout($request->uri);
		$view = $this->get_view($request);	

		try{
			$model->load($ID);
			$user = new User('');
			$user->load($model->user_id);
			$this->isSecured($user->user_id, $user->password);

			$view->parse($request->data, $model);
			$model->update();		
			
			$response->code = Response::OK;
			$response->addHeader('Location', $model->location());
		}catch (UnexpectedValueException $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
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

		$workout = new Workout($request->uri);

		try{
			$workout->load($ID);
			$user = new User('');
			$user->load($workout->user_id);
			$this->isSecured($user->user_id, $user->password);

			$workout->delete();
			$response->code = Response::NOCONTENT;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		}
		return $response;
	}
    
}
?>
