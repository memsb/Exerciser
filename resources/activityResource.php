<?php

require_once dirname(__FILE__) . '/../classes/activity.php';

/**
 * @namespace Tonic\Exerciser\Activities\Activity
 * @uri /activities/([0-9]+)/
 */
class ActivityResource extends Resource {

	private $views = array(	'xml' => 'xmlActivity', 
				'json' => 'jsonActivity', 
				'yaml' => 'yamlActivity'
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
		
		$model = new Activity();
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
}
?>
