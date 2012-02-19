<?php

require_once('/var/www/exerciser/tests/resources/resourceTest.php');
require_once('/var/www/exerciser/resources/activitiesResource.php');
require_once('/var/www/exerciser/classes/activities.php');

class TestOfActivitiesResource extends TestOfResource {

	protected $uri = '/activities/';
	protected $type = 'activities';
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
