<?php

require_once dirname(__FILE__) . '/../classes/activities.php';

/**
 * @namespace Tonic\Exerciser\Activities
 * @uri /activities/
 */
class ActivitiesResource extends Resource {

	private $views = array(	'xml' => 'xmlActivities', 
				'json' => 'jsonActivities', 
				'yaml' => 'yamlActivities'
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

		$model = new Activities();

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
}
?>
