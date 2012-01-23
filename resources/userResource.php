<?php

require_once dirname(__FILE__) . '/../classes/user.php';

/**
 * @namespace Tonic\Exerciser\Username
 * @uri /([A-Za-z0-9_]+)
 */
class UsernameResource extends Resource {

	private $views = array(	'xml' => 'xmlUser', 
				'json' => 'jsonUser', 
				'yaml' => 'yamlUser'
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

		$model = new User();
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

		$model = new User();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];

		try{
			$user->load($ID);
			$view->parse($request->data, $model);
			$model->update();		
	
			$response->code = Response::CREATED;
			$response->body = $model->location();
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
			return $response;
		}
		return $response;  
	}

	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	function post($request){	
		$response = new Response($request);
		try{
			$view = $this->get_view($request);
		}catch (Exception $e) {
			$response->code = Response::UNSUPPORTEDMEDIATYPE;
			return $response;
		}

		$model = new User();

		parse_str(urldecode($request->data), $data);
		$view->parse($request->data, $model);
		$model->create();
			
		$response->code = Response::CREATED;
		$response->addHeader('Content-type', $view->type());
		$response->body = $model->location();

		return $response;
	}

	/**
	* Handle a DELETE request for this resource
	* @param Request request
	* @return Response
	*/
	function delete($request) {
		$response = new Response($request);

		$model = new User();
		$bits = explode('/', $request->uri);
		$ID = $bits[count($bits)-1];

		try{
			$model->load($ID);
			$model->delete();

			$response = new Response($request);
			$response->code = Response::NOCONTENT; 
			$response->body = $model->username;
		}catch (Exception $e) {
			$response->code = Response::NOTFOUND;
		} 

		return $response;
	}
    
}
?>
