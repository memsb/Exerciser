<?php

require_once(CLASSES . 'workouts.php');
require_once(LIB . 'DBA.php');

/*
 * @namespace Exerciser\Tests\Models
 */
class TestOfWorkoutsClass extends UnitTestCase {

	private $workouts;
	private $uri = 'workouts';
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
		$this->db->setReturnValue('query', array(array( 'Workout_ID' => $this->fields['workout_id']['value'])) );
		$this->workouts = new Workouts($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->workouts->location(), '');
		$this->workouts->setLocation($this->uri);
		$this->assertEqual($this->workouts->location(), $this->uri);
	}

	function testNotLoaded() {
		$this->workouts->setLocation($this->uri);
		$this->assertEqual($this->workouts->size(), 0);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$workouts = new workouts(NULL);
	}

	function testLoadFailure(){	
		$this->db = &new MockDatabase();			
		$this->db->setReturnValue('query', array());
		$this->workouts = new Workouts($this->db);
		$this->workouts->load(1);
		$this->assertEqual($this->workouts->size(), 0);
	}

	function testSize(){
		$this->assertEqual($this->workouts->size(), 0);
		$this->workouts->addWorkout(new Workout($this->db));
		$this->assertEqual($this->workouts->size(), 1);
		$this->workouts->addWorkout(new Workout($this->db));
		$this->assertEqual($this->workouts->size(), 2);
	}

	function testRetrieve(){		
		$result = $this->workouts->retrieve(1);
		$this->assertNotNull($result);	
		$workout = $result[0];
		$this->assertEqual($workout['Workout_ID'], $this->fields['workout_id']['value']);
	}

	function testDeleteFailure(){
		$this->db = &new MockDatabase();			
		$this->db->setReturnValue('query', False);
		$this->workouts = new Workouts($this->db);
		$this->expectException('Exception');
		$this->workouts->delete(1);
	}

	function testDeleteSuccess(){
		$this->db = &new MockDatabase();			
		$this->db->setReturnValue('query', True);
		$this->workouts = new Workouts($this->db);
		$this->assertTrue($this->workouts->delete(1));
	}

}

/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfWorkoutsView extends UnitTestCase {

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
	protected $workouts;
	protected $workout;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');		
		$this->workouts = new Workouts($this->db);

		$this->workout = new Workout($this->db);
		foreach( $this->fields as $key => $value )
			$this->workout->$key = $value['value'];
		$this->workouts->addWorkout( $this->workout );
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->workouts->parse(NULL, NULL);
	}

	protected function createDoc(){
		$doc = $this->workouts->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}
}

/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfXMLWorkoutsClass extends TestOfWorkoutsView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->workouts->type($this->view), 'application/xml+workouts');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = simplexml_load_string($doc);
		$this->assertNotNull($data);
		$workout = (array)$data->workout;
		foreach( $this->fields as $key => $value )
			$this->assertEqual($workout[$key], $value['value']);	
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->workouts->generateDocument($this->view));
	}
}


/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfJSONWorkoutsClass extends TestOfWorkoutsView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->workouts->type($this->view), 'application/json+workouts');
	}

	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = json_decode($doc, true);
		$this->assertNotNull($data);
		$item = $data['workouts'][0];
		$this->assertNotNull($item);
		$workout = $item['workout'];
		$this->assertNotNull($workout);

		foreach( $this->fields as $key => $value )
			$this->assertEqual($workout[$key], $value['value']);
	}
}

/*
 * @namespace Exerciser\Tests\Views
 */
class TestOfYAMLWorkoutsClass extends TestOfWorkoutsView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->workouts->generateDocument($this->view), '');
	}

	function testDocumentType() {
		$this->assertEqual($this->workouts->type($this->view), 'text/x-yaml+workouts');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = yaml_parse($doc);
		$this->assertNotNull($data);
		$item = $data['workouts'][0];
		$this->assertNotNull($item);
		$workout = $item['workout'];
		$this->assertNotNull($workout);

		foreach( $this->fields as $key => $value )
			$this->assertEqual($workout[$key], $value['value']);
	}
}

?>
