<?php

require_once('/var/www/exerciser/tests/resources/resourceTest.php');
require_once('/var/www/exerciser/resources/workoutsResource.php');
require_once('/var/www/exerciser/classes/workouts.php');
require_once('/var/www/exerciser/classes/user.php');

class TestOfWorkoutsResource extends TestOfResource {

	protected $uri = '/users/1/workouts/';
	protected $type = 'workouts';
	protected $doc = 'doc';

	protected $user;
	protected $workout;

	protected $user_id = 1;
	protected $password = 'password';

	function setup() {
		parent::init();
		Mock::generate('Workouts');
		Mock::generate('User');
		$this->user = &new MockUser();	
	}

	protected function setModels($resource){
		$resource->setModel($this->model);
		$resource->setUser($this->user);
	}

	protected function get_auth_user(){
		$user = &new MockUser();
		$user->user_id = $this->user_id;
		$user->api_key = $this->password;
		$_SERVER['PHP_AUTH_USER'] = $this->user_id; 
		$_SERVER['PHP_AUTH_PW'] = $this->password;
		return $user;
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

	function testDeleteCodes() {
		$this->method = 'DELETE';
		$this->user = &new MockUser();
		$this->user->throwOn('load');
		$this->model = &new MockWorkouts();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->user = $this->get_auth_user();

		$this->model = &new MockWorkouts();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOCONTENT);

		$this->model = &new MockWorkouts();
		$this->model->throwOn('delete');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->user = &new MockUser();
		$this->expectException('ResponseException');
		$response = $this->getResponse();		
	}

	function testGetType() {
		$this->method = 'GET';
		$response = $this->getResponse();
		$this->assertEqual($response->headers['Content-type'], $this->type);
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

	function testPutBody() {
		$this->PutBody();
	}

	function testDeleteBody() {
		$this->DeleteBody();
	}
}

?>
