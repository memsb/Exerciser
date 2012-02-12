<?php

require_once('/var/www/exerciser/resources/activityResource.php');

class TestOfActivityResource extends UnitTestCase {

	private $config = array();
	private $uri = '/activities/1';
	private $res;
	private $activity;

	protected static function getMethod($name) {
		$class = new ReflectionClass('activityResource');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	function setup() {
		Mock::generate('Activity');
		$this->activity = &new MockActivity();
		$this->config = array('baseUri' => URI);
		$this->res = new ActivityResource($this->config);
	}

	function testSecured() {
		$method = self::getMethod('isSecured');
		$this->expectException('ResponseException');
		$method->invokeArgs($this->res, array('user', 'pass'));
	}

	function testViews() {
		$config = array();
		$this->assertEqual($this->get_view($config), 'xmlActivity');
		$config = array('uri' => '.xml');
		$this->assertEqual($this->get_view($config), 'xmlActivity');
		$config = array('uri' => '.json');
		$this->assertEqual($this->get_view($config), 'jsonActivity');
		$config = array('uri' => '.yaml');
		$this->assertEqual($this->get_view($config), 'yamlActivity');
	}

	function testGetCodes() {
		$response = $this->getResponse($this->uri);
		$this->assertEqual($response->code, '200');
		$response = $this->getResponse($this->uri . '.xml');
		$this->assertEqual($response->code, '200');
		$response = $this->getResponse($this->uri . '.json');
		$this->assertEqual($response->code, '200');
		$response = $this->getResponse($this->uri . '.yaml');
		$this->assertEqual($response->code, '200');

		$this->activity->throwOn('load');
		$this->res->setModel($this->activity);
		$response = $this->getResponse($this->uri);
		$this->assertEqual($response->code, '404');		
	}

	function testGetMimes() {
		$response = $this->getResponse($this->uri);
		$view = new xmlActivity();		
		$this->assertEqual($response->headers['Content-type'], $view->type());
		$response = $this->getResponse($this->uri . '.xml');
		$view = new xmlActivity();		
		$this->assertEqual($response->headers['Content-type'], $view->type());
		$response = $this->getResponse($this->uri . '.json');
		$view = new jsonActivity();		
		$this->assertEqual($response->headers['Content-type'], $view->type());
		$response = $this->getResponse($this->uri . '.yaml');
		$view = new yamlActivity();		
		$this->assertEqual($response->headers['Content-type'], $view->type());
	}

	function testGetBody() {
		$response = $this->getResponse($this->uri);
		$this->assertNotNull($response->body);	
	}

	private function getResponse($uri){
		$config = array('uri' => $uri);
		$request = new Request($config);
		$resource = $request->loadResource();
		$resource->setModel($this->activity);
		$response = $resource->exec($request);
		return $response;
	}

	private function get_view($config) {
		$request = new Request($config);
		$method = self::getMethod('get_view');
		$view = $method->invokeArgs($this->res, array($request));
		return get_class($view);
	}
}

?>
