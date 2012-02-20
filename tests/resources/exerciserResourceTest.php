<?php

require_once(TEST_RESOURCES . 'resourceTest.php');
require_once(RESOURCES . 'exerciserResource.php');
require_once(CLASSES . 'stats.php');

/*
 * @namespace Exerciser\Tests\Resources
 */
class TestOfExerciserResource extends TestOfResource {

	protected $uri = '/';
	protected $type = 'stats';
	protected $doc = 'doc';

	function setup() {
		parent::init();
	}

	function testGetCodes() {
		$this->method = 'GET';
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);
		
		$this->config['uri'] = URI . $this->uri . '.xml';
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);
		
		$this->config['uri'] = URI . $this->uri . '.json';
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);

		$this->config['uri'] = URI . $this->uri . '.yaml';
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);

		$this->config['uri'] = URI . $this->uri;
		$this->model->throwOn('load');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);		
	}

	function testGetType() {
		$this->method = 'GET';
		$response = $this->getResponse();
		$this->assertEqual($response->headers['Content-type'], $this->type);
	}

	function testPostType() {
		$this->PostType();
	}

	function testPutType() {
		$this->PutType();
	}

	function testDeleteType() {
		$this->DeleteType();
	}

	function testGetBody() {
		$this->method = 'GET';
		$response = $this->getResponse();
		$this->assertEqual($response->body, $this->doc);	
	}

	function testPostBody() {
		$this->PostBody();
	}

	function testPutBody() {
		$this->PutBody();
	}

	function testDeleteBody() {
		$this->DeleteBody();
	}
}

?>
