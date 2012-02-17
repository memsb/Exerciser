<?php

require_once('/var/www/exerciser/classes/activity.php');
require_once('/var/www/exerciser/lib/DBA.php');

class TestOfActivityClass extends UnitTestCase {

	private $uri = 'uri';
	private $db;
	private $activity;

	protected $fields = array(
				'activity_id' => array('value' => 2, 'default' => 0), 
				'activity_name' => array('value' => 'name', 'default' => ''), 
				'description' => array('value' => 'description', 'default' => ''), 
				'kcal_hour' => array('value' => 200, 'default' => 0)
				);

	protected $Database_Mapping = array(
				'activity_id' => 'Activity_ID',
				'activity_name' => 'Activity_Name',
				'description' => 'Description',
				'kcal_hour' => 'Kcal_Hour'
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
		$this->activity = new Activity($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->activity->location(), '');
		$this->activity->setLocation($this->uri);
		$this->assertEqual($this->activity->location(), $this->uri);
	}

	function testNotLoaded() {
		$this->activity->setLocation($this->uri);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->activity->$key, $value['default']);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$activity = new Activity(NULL);
	}

	function testSetsAndGets() {
		foreach($this->fields as $key => $value)
			$this->activity->$key = $value['value'];

		foreach($this->fields as $key => $value)
			$this->assertEqual($this->activity->$key, $value['value']);
	}

	function testLoadFailure(){				
		$this->db->setReturnValue('query', array());
		$this->expectException('LengthException');
		$this->activity->load(0);
	}

	function testLoadSuccess(){
		$this->db->setReturnValue('query', array($this->toDB()));
		$this->activity->load(0);
		foreach($this->fields as $key => $value)
			$this->assertEqual($this->activity->$key, $value['value']);
	}
}

class TestOfActivityView extends UnitTestCase {

	protected $view;
	protected $db;
	protected $activity;

	protected $fields = array(
			'activity_id' => array('value' => 2, 'default' => 0), 
			'activity_name' => array('value' => 'name', 'default' => ''), 
			'description' => array('value' => 'description', 'default' => ''), 
			'kcal_hour' => array('value' => 200, 'default' => 0)
			);

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');		
		$this->activity = new Activity($this->db);
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->activity->parse(NULL, NULL);
	}

	protected function createDoc(){
		foreach($this->fields as $key => $value)
			$this->activity->$key = $value['value'];

		$doc = $this->activity->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}
}

class TestOfXMLActivityClass extends TestOfActivityView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->activity->type($this->view), 'application/xml+activity');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = (array)simplexml_load_string($doc);
		$this->assertNotNull($data);		

		foreach($this->fields as $key => $value)
			$this->assertEqual($this->activity->$key, $value['value']);
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->activity->generateDocument($this->view), '');
	}
}


class TestOfJSONActivityClass extends TestOfActivityView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->activity->type($this->view), 'application/json+activity');
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->activity->generateDocument($this->view), '');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$item = json_decode($doc, true);
		$data = $item['activity'];
		$this->assertNotNull($data);

		foreach($this->fields as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}
}


class TestOfYAMLActivityClass extends TestOfActivityView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->activity->generateDocument($this->view), '');
	}

	function testDocumentType() {
		$this->assertEqual($this->activity->type($this->view), 'text/x-yaml+activity');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$item = yaml_parse($doc);
		$data = $item['activity'];
		$this->assertNotNull($data);

		foreach($this->fields as $key => $value)
			$this->assertEqual($data[$key], $value['value']);
	}
}

?>
