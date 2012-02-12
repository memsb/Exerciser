<?php

require_once('/var/www/exerciser/classes/activity.php');
require_once('/var/www/exerciser/lib/DBA.php');

class TestOfActivityClass extends UnitTestCase {

	private $id = 7;
	private $name = 'test';
	private $kcal = 70;
	private $desc = 'testing';
	private $uri = 'test/url/';
	private $db;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
	}

	function testResourceLocation() {
		$activity = new Activity();
		$this->assertEqual($activity->location(), '');
		$activity->setLocation($this->uri);
		$this->assertEqual($activity->location(), $this->uri);
	}

	function testNotLoaded() {
		$activity = new Activity();
		$activity->setLocation($this->uri);
		$this->assertEqual($activity->activity_id, 0);
		$this->assertEqual($activity->activity_name, '');
		$this->assertEqual($activity->description, '');
		$this->assertEqual($activity->kcal_hour, 0);
	}

	function testNoDatabase(){
		$activity = new Activity();
		$activity->setDatabase(NULL);
		$this->assertEqual($activity->db, NULL);

		$this->expectException('BadMethodCallException');
		$activity->load(0);
	}

	function testSetsAndGets() {		
		$activity = new Activity();
		$activity->activity_id = $this->id;
		$activity->activity_name = $this->name;
		$activity->kcal_hour = $this->kcal;
		$activity->description = $this->desc;

		$this->assertEqual($activity->activity_id, $this->id);
		$this->assertEqual($activity->activity_name, $this->name);
		$this->assertEqual($activity->description, $this->desc);
		$this->assertEqual($activity->kcal_hour, $this->kcal);
	}

	function testLoadFailure(){				
		$this->db->setReturnValue('query', array());

		$activity = new Activity();
		$activity->setDatabase($this->db);
		$this->expectException('LengthException');
		$activity->load(0);
	}

	function testLoadSuccess(){
		$this->db->setReturnValue('query', array(
							'Activity_ID' => $this->id,
							'Activity_name' => $this->name,
							'Description' => $this->desc,
							'kcal_hour' => $this->kcal
						));

		$activity = new Activity();
		$activity->setDatabase($this->db);
		$activity->load(0);
		$this->assertEqual($activity->activity_id, $this->id);
		$this->assertEqual($activity->activity_name, $this->name);
		$this->assertEqual($activity->description, $this->desc);
		$this->assertEqual($activity->kcal_hour, $this->kcal);
	}
}

class TestOfActivityView extends UnitTestCase {

	protected $view;
	protected $activity;
	protected $id = 7;
	protected $name = 'test';
	protected $kcal = 70;
	protected $desc = 'testing';
	protected $uri = 'test/url/';

	function setup(){		
		$this->activity = new Activity();
		$this->activity->activity_id = $this->id;
		$this->activity->activity_name = $this->name;
		$this->activity->description = $this->desc;
		$this->activity->kcal_hour = $this->kcal;
	}
}


class TestOfXMLAcivityClass extends TestOfActivityView {

	function setup(){
		parent::setup();
		$this->view = new xmlActivity();		
	}

	function testDocumentType() {
		$this->assertEqual($this->view->type(), 'application/xml+activity');
	}

	function testParseDocument() {		
		$this->expectException('BadMethodCallException');
		$this->view->parse(NULL, NULL);
	}

	function testEmptyDocument() {
		$this->assertEqual($this->view->toString(), '');
	}
	
	function testGenerateDocument() {
		$this->view->generateDocument($this->activity);
		$doc = $this->view->toString();
		$this->assertNotNull($doc);

		$data = (array)simplexml_load_string($doc);
		$this->assertNotNull($data);

		$this->assertEqual($data['activity_id'], $this->id);
		$this->assertEqual($data['activity_name'], $this->name);
		$this->assertEqual($data['description'], $this->desc);
		$this->assertEqual($data['kcal_hour'], $this->kcal);
	}
}

class TestOfJSONAcivityClass extends TestOfActivityView {

	function setup(){
		parent::setup();
		$this->view = new jsonActivity();		
	}

	function testDocumentType() {
		$this->assertEqual($this->view->type(), 'application/json+activity');
	}

	function testParseDocument() {		
		$this->expectException('BadMethodCallException');
		$this->view->parse(NULL, NULL);
	}

	function testEmptyDocument() {
		$this->assertEqual($this->view->toString(), '');
	}
	
	function testGenerateDocument() {
		$this->view->generateDocument($this->activity);
		$doc = $this->view->toString();
		$this->assertNotNull($doc);

		$item = json_decode($doc, true);
		$data = $item['activity'];
		$this->assertNotNull($data);

		$this->assertEqual($data['activity_id'], $this->id);
		$this->assertEqual($data['activity_name'], $this->name);
		$this->assertEqual($data['description'], $this->desc);
		$this->assertEqual($data['kcal_hour'], $this->kcal);
	}
}

class TestOfYAMLAcivityClass extends TestOfActivityView {

	function setup(){
		parent::setup();
		$this->view = new yamlActivity();		
	}

	function testDocumentType() {
		$this->assertEqual($this->view->type(), 'text/x-yaml+activity');
	}

	function testParseDocument() {		
		$this->expectException('BadMethodCallException');
		$this->view->parse(NULL, NULL);
	}

	function testEmptyDocument() {
		$this->assertEqual($this->view->toString(), '');
	}
	
	function testGenerateDocument() {
		$this->view->generateDocument($this->activity);
		$doc = $this->view->toString();
		$this->assertNotNull($doc);

		$item = yaml_parse($doc);
		$data = $item['activity'];
		$this->assertNotNull($data);

		$this->assertEqual($data['activity_id'], $this->id);
		$this->assertEqual($data['activity_name'], $this->name);
		$this->assertEqual($data['description'], $this->desc);
		$this->assertEqual($data['kcal_hour'], $this->kcal);
	}
}

?>
