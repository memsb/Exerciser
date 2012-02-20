<?php

require_once(CLASSES . 'users.php');
require_once(LIB . 'DBA.php');

/*
 * @namespace Exerciser\Tests\Models
 */
class TestOfUsersClass extends UnitTestCase {

	private $users;
	private $uri = 'users';
	private $db;

	private $fields = array(
			'user_id' => array('value' => 2, 'default' => 0), 
			'username' => array('value' => 'username', 'default' => ''), 
			'user_id' => array('value' => 1, 'default' => 0), 
			'activity_id' => array('value' => 3, 'default' => 0), 
			'activity_name' => array('value' => 'activity', 'default' => ''), 
			'start' => array('value' => 123, 'default' => 0), 
			'duration' => array('value' => 20, 'default' => 0), 
			'kcal' => array('value' => 100, 'default' => 0)
			);

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', array(array( 'User_ID' => $this->fields['user_id']['value'])) );
		$this->users = new users($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->users->location(), '');
		$this->users->setLocation($this->uri);
		$this->assertEqual($this->users->location(), $this->uri);
	}

	function testNotLoaded() {
		$this->users->setLocation($this->uri);
		$this->assertEqual($this->users->size(), 0);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$users = new users(NULL);
	}

	function testLoadFailure(){	
		$this->db = &new MockDatabase();			
		$this->db->setReturnValue('query', array());
		$this->users = new users($this->db);
		$this->users->load(1);
		$this->assertEqual($this->users->size(), 0);
	}

	function testSize(){
		$this->assertEqual($this->users->size(), 0);
		$this->users->adduser(new user($this->db));
		$this->assertEqual($this->users->size(), 1);
		$this->users->adduser(new user($this->db));
		$this->assertEqual($this->users->size(), 2);
	}	
}

/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfusersView extends UnitTestCase {

	protected $fields = array(
				'user_id' => array('value' => 2, 'default' => 0), 
				'username' => array('value' => 'username', 'default' => ''),
				'age' => array('value' => 3, 'default' => 0), 
				'weight' => array('value' => 'activity', 'default' => ''), 
				'gender' => array('value' => 123, 'default' => 0)
				);

	protected $view;
	protected $db;
	protected $users;
	protected $user;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');		
		$this->users = new users($this->db);

		$this->user = new user($this->db);
		foreach( $this->fields as $key => $value )
			$this->user->$key = $value['value'];
		$this->users->adduser( $this->user );
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->users->parse(NULL, NULL);
	}

	protected function createDoc(){
		$doc = $this->users->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}
}

/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfXMLusersClass extends TestOfusersView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->users->type($this->view), 'application/xml+users');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = simplexml_load_string($doc);
		$this->assertNotNull($data);
		$user = (array)$data->user;
		foreach( $this->fields as $key => $value )
			$this->assertEqual($user[$key], $value['value']);	
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->users->generateDocument($this->view));
	}
}


/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfJSONusersClass extends TestOfusersView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->users->type($this->view), 'application/json+users');
	}

	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = json_decode($doc, true);
		$this->assertNotNull($data);
		$item = $data['users'][0];
		$this->assertNotNull($item);
		$user = $item['user'];
		$this->assertNotNull($user);

		foreach( $this->fields as $key => $value )
			$this->assertEqual($user[$key], $value['value']);
	}
}

/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfYAMLusersClass extends TestOfusersView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->users->generateDocument($this->view), '');
	}

	function testDocumentType() {
		$this->assertEqual($this->users->type($this->view), 'text/x-yaml+users');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = yaml_parse($doc);
		$this->assertNotNull($data);
		$item = $data['users'][0];
		$this->assertNotNull($item);
		$user = $item['user'];
		$this->assertNotNull($user);

		foreach( $this->fields as $key => $value )
			$this->assertEqual($user[$key], $value['value']);
	}
}


?>
