<?php

require_once('/var/www/exerciser/classes/user.php');
require_once('/var/www/exerciser/lib/DBA.php');

class TestOfUserClass extends UnitTestCase {

	private $uri = 'user';
	private $user;
	private $db;

	private $fields = array(
				'user_id' => array('value' => 2, 'default' => 0), 
				'username' => array('value' => 'username', 'default' => ''), 
				'api_key' => array('value' => 1, 'default' => 0), 
				'age' => array('value' => 3, 'default' => 0), 
				'weight' => array('value' => 'activity', 'default' => ''), 
				'gender' => array('value' => 123, 'default' => 0)
				);

	protected $Database_Mapping = array(
			'user_id' => 'User_ID',
			'username' => 'Username',
			'api_key' => 'API_Key',
			'age' => 'Age',
			'weight' => 'Weight',
			'gender' => 'Gender'
			);

	private function toDB(){
		$data = array();
		foreach( $this->Database_Mapping as $attribute_key => $database_key )
			$data[$database_key] = $this->fields[$attribute_key]['value'];
		return $data;
	}

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', array($this->toDB()));
		$this->user = new user($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->user->location(), '');
		$this->user->setLocation($this->uri);
		$this->assertEqual($this->user->location(), $this->uri);
	}

	function testNotLoaded() {
		$this->user->setLocation($this->uri);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->user->$key, $value['default']);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$user = new user(NULL);
	}

	function testSetsAndGets() {
		foreach($this->fields as $key => $value)
			$this->user->$key = $value['value'];

		foreach($this->fields as $key => $value)
			$this->assertEqual($this->user->$key, $value['value']);
	}

	function testLoadFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', array());
		$this->user = new User($this->db);
		$this->expectException('LengthException');
		$this->user->load(1);
	}

	function testLoadSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', array($this->toDB()));
		$id = $this->fields['user_id']['value'];
		$this->db->setReturnValue('clean', $id);
		$this->user = new User($this->db);
		$this->user->load($id);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->user->$key, $value['value']);
	}

	function testCreateFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', False);

		$this->user = new User($this->db);
		$this->expectException('Exception');
		$this->assertNotEqual($this->user->create(), True);
	}

	function testCreateSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', True);
		$this->db->setReturnValue('insert_id', $this->fields['user_id']['value']);

		$this->user = new User($this->db);
		foreach($this->fields as $key => $value)
			$this->user->$key = $value['value'];
		$this->assertEqual($this->user->create(), True);
		$this->assertEqual($this->user->user_id, $this->fields['user_id']['value']);
		$this->assertEqual($this->user->uri, '/' . $this->fields['user_id']['value']);
	}

	function testUpdateFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', False);

		$this->user = new user($this->db);
		$this->expectException('Exception');
		$this->assertNotEqual($this->user->update(), True);
	}

	function testUpdateSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', True);

		$this->user = new user($this->db);
		foreach($this->fields as $key => $value)
			$this->user->$key = $value['value'];
		$this->assertEqual($this->user->update(), True);
	}

	function testDeleteFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', False);		
		$this->user = new user($this->db);
		$id = $this->fields['user_id']['value'];
		$this->expectException('Exception');
		$this->assertNotEqual($this->user->delete($id), True);
	}

	function testDeleteSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', True);		
		$this->user = new user($this->db);
		$id = $this->fields['user_id']['value'];
		$this->assertEqual($this->user->delete($id), True);
	}

	function testValidation(){
		foreach($this->fields as $key => $value)
			$this->user->$key = NULL;
		$this->expectException('UnexpectedValueException');
		$this->user->validate();

		foreach($this->fields as $key => $value)
			$this->user->$key = $value['value'];
		$this->user->validate();
	}
}

class TestOfUserView extends UnitTestCase {

	protected $input = array(
				'username' => array('value' => 'username', 'default' => ''), 
				'age' => array('value' => 3, 'default' => 0), 
				'weight' => array('value' => 'activity', 'default' => ''), 
				'gender' => array('value' => 123, 'default' => 0)
				);

	protected $user_data = array(
				'user_id' => array('value' => 2, 'default' => 0), 
				'username' => array('value' => 'username', 'default' => ''),				
				'age' => array('value' => 3, 'default' => 0), 
				'weight' => array('value' => 'activity', 'default' => ''), 
				'gender' => array('value' => 123, 'default' => 0)
				);

	protected $registration_data = array(
				'user_id' => array('value' => 2, 'default' => 0),			
				'api_key' => array('value' => 1, 'default' => 0)
				);

	protected $view;
	protected $db;
	protected $user;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');		
		$this->user = new user($this->db);
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->user->parse(NULL, NULL);
	}

	protected function createNewUserDoc($data){
		foreach($data as $key => $value)
			$this->user->$key = $value['value'];

		$doc = $this->user->generateNewUserDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}

	protected function createDoc($data){
		foreach($data as $key => $value)
			$this->user->$key = $value['value'];

		$doc = $this->user->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}

	protected function EmptyDocument() {
		$this->assertNotNull($this->user->generateDocument($this->view));
	}

	protected function ParseEmptyDocument() {		
		$this->expectException('Exception');
		$this->user->parse(NULL, $this->view);
	}

	protected function ParseDocumentFailure() {		
		$this->expectException('UnexpectedValueException');
		$this->user->parse(NULL, $this->view);
	}

	protected function ParseDocument() {
		$doc = $this->createDoc($this->input);
		$this->user->parse($doc, $this->view);
		foreach($this->input as $key => $value)
			$this->assertEqual($this->user->$key, $value['value']);
	}
}

class TestOfXMLUserClass extends TestOfUserView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->user->type($this->view), 'application/xml+user');
	}

	function testEmptyDocument() {
		parent::EmptyDocument();
	}

	function testParseEmptyDocument() {		
		parent::ParseEmptyDocument();
	}

	function testParseDocumentFailure() {
		parent::ParseDocumentFailure();
	}

	function testParseDocument() {
		parent::ParseDocument();
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc($this->user_data);

		$data = (array)simplexml_load_string($doc);
		$this->assertNotNull($data);
		
		foreach($this->user_data as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}

	function testGenerateRegistrationDocument() {
		$doc = $this->createNewUserDoc($this->registration_data);

		$data = (array)simplexml_load_string($doc);
		$this->assertNotNull($data);
		
		foreach($this->registration_data as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}

}

class TestOfJSONUserClass extends TestOfUserView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->user->type($this->view), 'application/json+user');
	}

	function testEmptyDocument() {
		parent::EmptyDocument();
	}

	function testParseEmptyDocument() {		
		parent::ParseEmptyDocument();
	}

	function testParseDocumentFailure() {
		parent::ParseDocumentFailure();
	}

	function testParseDocument() {
		parent::ParseDocument();
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc($this->user_data);

		$item = json_decode($doc, true);
		$data = $item['user'];
		$this->assertNotNull($data);

		foreach($this->user_data as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}

	function testGenerateRegistrationDocument() {
		$doc = $this->createNewUserDoc($this->registration_data);

		$item = json_decode($doc, true);
		$data = $item['new_user'];
		$this->assertNotNull($data);
		
		foreach($this->registration_data as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}
}


class TestOfYAMLUserClass extends TestOfUserView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->user->type($this->view), 'text/x-yaml+user');
	}

	function testEmptyDocument() {
		parent::EmptyDocument();
	}

	function testParseEmptyDocument() {		
		parent::ParseEmptyDocument();
	}

	function testParseDocumentFailure() {
		parent::ParseDocumentFailure();
	}

	function testParseDocument() {
		parent::ParseDocument();
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc($this->user_data);

		$item = yaml_parse($doc);
		$data = $item['user'];
		$this->assertNotNull($data);

		foreach($this->user_data as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}

	function testGenerateRegistrationDocument() {
		$doc = $this->createNewUserDoc($this->registration_data);

		$item = yaml_parse($doc);
		$data = $item['new_user'];
		$this->assertNotNull($data);
		
		foreach($this->registration_data as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}
}

?>
