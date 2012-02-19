<?php

require_once('/var/www/exerciser/classes/workout.php');
require_once('/var/www/exerciser/lib/DBA.php');

class TestOfWorkoutClass extends UnitTestCase {

	private $uri = 'workout';
	private $workout;
	private $db;
	private $fields = array(
				'workout_id' => array('value' => 2, 'default' => 0), 
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
		$this->db->setReturnValue('query', array());
		$this->workout = new Workout($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->workout->location(), '');
		$this->workout->setLocation($this->uri);
		$this->assertEqual($this->workout->location(), $this->uri);
	}

	function testNotLoaded() {
		$this->workout->setLocation($this->uri);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->workout->$key, $value['default']);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$workout = new Workout(NULL);
	}

	function testSetsAndGets() {
		foreach($this->fields as $key => $value)
			$this->workout->$key = $value['value'];

		foreach($this->fields as $key => $value)
			$this->assertEqual($this->workout->$key, $value['value']);
	}

	function testLoadFailure(){
		$this->expectException('LengthException');
		$this->workout->load(1);
	}

	function testLoadSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', array(array(
							'Username' => $this->fields['username']['value'],
							'User_ID' => $this->fields['user_id']['value'],
							'Activity_ID' => $this->fields['activity_id']['value'],
							'Activity_Name' => $this->fields['activity_name']['value'],
							'Start_Time' => $this->fields['start']['value'],
							'Duration' => $this->fields['duration']['value'],
							'Kcal' => $this->fields['kcal']['value']
						)));
		$id = $this->fields['workout_id']['value'];
		$this->db->setReturnValue('clean', $id);
		$this->workout = new Workout($this->db);
		$this->workout->load($id);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->workout->$key, $value['value']);
	}

	function testCreateFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', False);

		$this->workout = new Workout($this->db);
		$this->expectException('Exception');
		$this->assertNotEqual($this->workout->create(), True);
	}

	function testCreateSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', True);
		$this->db->setReturnValue('insert_id', $this->fields['workout_id']['value']);

		$this->workout = new Workout($this->db);
		foreach($this->fields as $key => $value)
			$this->workout->$key = $value['value'];
		$this->assertEqual($this->workout->create(), True);
		$this->assertEqual($this->workout->workout_id, $this->fields['workout_id']['value']);
		$this->assertEqual($this->workout->uri,'/' . $this->fields['workout_id']['value']);
	}

	function testUpdateFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', False);

		$this->workout = new Workout($this->db);
		$this->expectException('Exception');
		$this->assertNotEqual($this->workout->update(), True);
	}

	function testUpdateSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', True);

		$this->workout = new Workout($this->db);
		foreach($this->fields as $key => $value)
			$this->workout->$key = $value['value'];
		$this->assertEqual($this->workout->update(), True);
	}

	function testDeleteFailure(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', False);		
		$this->workout = new Workout($this->db);
		$id = $this->fields['workout_id']['value'];
		$this->expectException('Exception');
		$this->assertNotEqual($this->workout->delete($id), True);
	}

	function testDeleteSuccess(){
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->db->setReturnValue('query', True);		
		$this->workout = new Workout($this->db);
		$id = $this->fields['workout_id']['value'];
		$this->assertEqual($this->workout->delete($id), True);
	}

	function testValidation(){
		$this->expectException('Exception');
		$this->workout->validate();

		foreach($this->fields as $key => $value)
			$this->workout->$key = $value['value'];
		$this->workout->validate();
	}
}

class TestOfWorkoutView extends UnitTestCase {

	protected $fields = array(
				'workout_id' => array('value' => 2, 'default' => 0), 
				'username' => array('value' => 'username', 'default' => ''), 
				'user_id' => array('value' => 1, 'default' => 0), 
				'activity_id' => array('value' => 3, 'default' => 0), 
				'activity_name' => array('value' => 'activity', 'default' => ''), 
				'start' => array('value' => 123, 'default' => 0), 
				'duration' => array('value' => 20, 'default' => 0), 
				'kcal' => array('value' => 100, 'default' => 0)
				);

	protected $view;
	protected $db;
	protected $workout;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');		
		$this->workout = new Workout($this->db);
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->workout->parse(NULL, NULL);
	}

	protected function createDoc(){
		foreach($this->fields as $key => $value)
			$this->workout->$key = $value['value'];

		$doc = $this->workout->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}

	protected function EmptyDocument() {
		$this->assertNotNull($this->workout->generateDocument($this->view));
	}

	protected function ParseEmptyDocument() {		
		$this->expectException('Exception');
		$this->workout->parse(NULL, $this->view);
	}

	protected function ParseDocumentFailure() {
		$doc = $this->createDoc();		
		$this->expectException('UnexpectedValueException');
		$this->workout->parse(NULL, $this->view);
	}

	protected function ParseDocument() {
		$doc = $this->createDoc();
		$this->workout->parse($doc, $this->view);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->workout->$key, $value['value']);
	}
}

class TestOfXMLWorkoutClass extends TestOfWorkoutView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->workout->type($this->view), 'application/xml+workout');
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
		$doc = $this->createDoc();

		$data = (array)simplexml_load_string($doc);
		$this->assertNotNull($data);

		foreach($this->fields as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}

}

class TestOfJSONWorkoutClass extends TestOfWorkoutView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->workout->type($this->view), 'application/json+workout');
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
		$doc = $this->createDoc();

		$item = json_decode($doc, true);
		$data = $item['workout'];
		$this->assertNotNull($data);

		foreach($this->fields as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}
}


class TestOfYAMLWorkoutClass extends TestOfWorkoutView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->workout->type($this->view), 'text/x-yaml+workout');
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
		$doc = $this->createDoc();

		$item = yaml_parse($doc);
		$data = $item['workout'];
		$this->assertNotNull($data);

		foreach($this->fields as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}
}

?>
