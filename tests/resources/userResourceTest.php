<?php

require_once(TEST_RESOURCES . 'resourceTest.php');
require_once(RESOURCES . 'userResource.php');
require_once(CLASSES . 'user.php');

/*
 * @namespace Exerciser\Tests\Resources
 */
class TestOfUserResource extends TestOfResource {

	protected $uri = '/users/1';
	protected $type = 'user';
	protected $data = 'doc';

	protected $user;

	protected $user_id = 1;
	protected $password = 'password';

	function setup() {
		parent::init();
		Mock::generate('User');
		$this->model = &new MockUser();	
		$this->model->setReturnValue('type', $this->type);
		$this->model->setReturnValue('generateDocument', $this->doc);
		$this->model->setReturnValue('generateNewUserDocument', $this->doc);
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

		$this->model = $this->get_auth_user();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::CREATED);

		$this->model = $this->get_auth_user();
		$this->model->throwOn('parse');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);

		$this->model = $this->get_auth_user();
		$this->model->throwOn('create');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);
	}

	function testPutCodes(){
		$this->method = 'PUT';

		$this->expectException('ResponseException');
		$response = $this->getResponse();

		$this->model = &new MockUser();
		$this->model->throwOn('load');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->model = $this->get_auth_user();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::OK);

		$this->model = $this->get_auth_user();
		$this->model->throwOn('parse', new UnexpectedValueException());
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::UNSUPPORTEDMEDIATYPE);

		$this->model = $this->get_auth_user();
		$this->model->throwOn('update');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);
	}

	function testDeleteCodes(){
		$this->method = 'DELETE';
	
		$this->expectException('ResponseException');
		$response = $this->getResponse();

		$this->model = $this->get_auth_user();
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOCONTENT);

		$this->model = $this->get_auth_user();
		$this->model->throwOn('load');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);

		$this->model = $this->get_auth_user();
		$this->model->throwOn('delete');
		$response = $this->getResponse();
		$this->assertEqual($response->code, Response::NOTFOUND);		
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
