<?php

require_once(TEST_RESOURCES . 'resourceTest.php');
require_once(RESOURCES . 'workoutResource.php');
require_once(CLASSES . 'workout.php');
require_once(CLASSES . 'user.php');

class TestOfWorkoutResource extends TestOfResource {

	protected $uri = '/workouts/1';
	protected $type = 'workout';
	protected $data = 'doc';

	protected $user;
	protected $workout;

	protected $user_id = 1;
	protected $password = 'password';

	function setup() {
		parent::init();
		Mock::generate('Workout');
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
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);
		$response = $this->getResponse($this->uri . '.xml');
		$this->assertEqual($response->code, Response::OK);
		$response = $this->getResponse($this->uri . '.json');
		$this->assertEqual($response->code, Response::OK);
		$response = $this->getResponse($this->uri . '.yaml');
		$this->assertEqual($response->code, Response::OK);

		$this->model->throwOn('load');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);		
	}

	function testPostCodes(){
		$this->method = 'POST';
		$this->user = &new MockUser();
		$this->user->throwOn('load');
		$this->model = &new MockWorkout();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);

		$this->user = $this->get_auth_user();

		$this->model = &new MockWorkout();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::CREATED);

		$this->model = &new MockWorkout();
		$this->model->throwOn('parse');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);

		$this->model = &new MockWorkout();
		$this->model->throwOn('create');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);

		$this->user = &new MockUser();
		$this->expectException('ResponseException');
		$response = $this->getResponse();
	}

	function testPutCodes(){
		$this->method = 'PUT';
		$this->user = &new MockUser();
		$this->user->throwOn('load');
		$this->model = &new MockWorkout();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->user = $this->get_auth_user();

		$this->model = &new MockWorkout();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);

		$this->model = &new MockWorkout();
		$this->model->throwOn('load');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->model = &new MockWorkout();
		$this->model->throwOn('parse', new UnexpectedValueException());
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);

		$this->model = &new MockWorkout();
		$this->model->throwOn('update');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->user = &new MockUser();
		$this->model = &new MockWorkout();
		$this->expectException('ResponseException');
		$response = $this->getResponse();
	}

	function testDeleteCodes(){
		$this->method = 'DELETE';
		$this->user = &new MockUser();
		$this->user->throwOn('load');
		$this->model = &new MockWorkout();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->user = $this->get_auth_user();

		$this->model = &new MockWorkout();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOCONTENT);

		$this->model = &new MockWorkout();
		$this->model->throwOn('load');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->model = &new MockWorkout();
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

	function testGetBody() {
		$this->method = 'GET';
		$response = $this->getResponse();
		$this->assertEqual($response->body, $this->doc);	
	}
}

?>
