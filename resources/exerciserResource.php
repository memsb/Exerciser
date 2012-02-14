<?php

require_once dirname(__FILE__) . '/../classes/stats.php';
require_once dirname(__FILE__) . '/../lib/MultiViewResource.php';

/**
 * @namespace Tonic\Exerciser
 * @uri /
 */
class ExerciserResource extends MultiViewResource {

	protected $views = array(	'xml' => 'xmlStats', 
					'json' => 'jsonStats', 
					'yaml' => 'yamlStats'
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

		$view = $this->get_view($request);
		$model = new Stats($request->uri);

		try{
			$model->load();			
			$view->generateDocument($model);
			
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
