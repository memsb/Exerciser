<?php

require_once dirname(__FILE__) . '/../classes/stats.php';

/**
 * @namespace Tonic\Exerciser
 * @uri /
 */
class ExerciserResource extends Resource {

	private $views = array(	'xml' => 'xmlStats', 
				'json' => 'jsonStats', 
				'yaml' => 'yamlStats'
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
		$view = $this->get_view($request);

		$model = new Stats();

		try{
			$model->load();		
		
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
		$test = new UserResource(null);
		$response = $test->post($request);
		return $response;
	}    
}

?>
