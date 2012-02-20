<?php

require_once(LIB . 'CRUD.php');
require_once(CLASSES . 'user.php');

/*
 * @namespace Exerciser\Tests\Resources
 */
class TestOfResource extends UnitTestCase {

	protected $config = array();
	protected $uri = '/';
	protected $type = '';
	protected $doc = '';
	protected $res;
	protected $model;
	protected $method = 'GET';
	protected $data = '';

	protected function init(){
		Mock::generate('CRUD');
		$this->model = &new MockCRUD();
		$this->model->setReturnValue('type', $this->type);
		$this->model->setReturnValue('generateDocument', $this->doc);
	}

	protected static function getMethod($class, $name) {
		$class = new ReflectionClass($class);
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	protected function getResponse(){
		$config = array('baseUri' => URI, 'uri' => URI . $this->uri, 'method' => $this->method, 'data' => $this->data);
		$request = new Request($config);		
		$resource = $request->loadResource();		
		$this->setModels($resource);
		$response = $resource->exec($request);
		return $response;
	}

	protected function setModels($resource){
		$resource->setModel($this->model);
	}

	protected function GetType() {
		$this->method = 'GET';
		$this->expectException('ResponseException');
		$response = $this->getResponse();
	}

	protected function PostType() {
		$this->method = 'POST';
		$this->expectException('ResponseException');
		$response = $this->getResponse();
	}

	protected function PutType() {
		$this->method = 'PUT';
		$this->expectException('ResponseException');
		$response = $this->getResponse();
	}

	protected function DeleteType() {
		$this->method = 'DELETE';
		$this->expectException('ResponseException');
		$response = $this->getResponse();
	}

	protected function GetBody() {
		$this->method = 'GET';
		$this->expectException('ResponseException');
		$response = $this->getResponse();	
	}

	protected function PostBody() {
		$this->method = 'POST';
		$this->expectException('ResponseException');
		$response = $this->getResponse();	
	}

	protected function PutBody() {
		$this->method = 'PUT';
		$this->expectException('ResponseException');
		$response = $this->getResponse();	
	}

	protected function DeleteBody() {
		$this->method = 'DELETE';
		$this->expectException('ResponseException');
		$response = $this->getResponse();	
	}
}

?>
