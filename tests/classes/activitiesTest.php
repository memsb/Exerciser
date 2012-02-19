<?php

require_once('/var/www/exerciser/classes/activities.php');
require_once('/var/www/exerciser/lib/DBA.php');

class TestOfActivitiesClass extends UnitTestCase {
	
	private $uri = 'test/url/';
	private $db;
	private $activities;

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
		$this->db->setReturnValue('query', array($this->toDB()));
		$this->activities = new Activities($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->activities->location(), '');
		$this->activities->setLocation($this->uri);
		$this->assertEqual($this->activities->location(), $this->uri);
	}

	function testNotLoaded() {
		$this->activities->setLocation($this->uri);
		$this->assertEqual($this->activities->size(), 0);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$activities = new activities(NULL);
	}

	function testLoadFailure(){	
		$this->db = &new MockDatabase();			
		$this->db->setReturnValue('query', array());
		$this->activities = new Activities($this->db);
		$this->activities->load();
		$this->assertEqual($this->activities->size(), 0);
	}

	function testLoadSuccess(){		
		$this->activities->load();
		$this->assertEqual($this->activities->size(), 1);
		$activities = $this->activities->getActivities();		
		$activity = $activities[0];
		foreach($this->fields as $key => $value)
			$this->assertEqual($activity->$key, $value['value']);
	}

}

class TestOfactivitiesView extends UnitTestCase {

	protected $uri = 'test/url/';
	protected $view;
	protected $db;
	protected $activities;

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

	protected function toDB(){
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
		$this->activities = new Activities($this->db);
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->activities->parse(NULL, NULL);
	}

	protected function createDoc(){
		$this->activities->load();

		$doc = $this->activities->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}
}

class TestOfXMLAcivitiesClass extends TestOfactivitiesView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->activities->type($this->view), 'application/xml+activities');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = simplexml_load_string($doc);
		$this->assertNotNull($data);
		$activity = (array)$data->activity;
		foreach($this->fields as $key => $value)
			$this->assertEqual($activity[$key], $value['value']);
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->activities->generateDocument($this->view), '');
	}
}


class TestOfJSONAcivitiesClass extends TestOfactivitiesView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->activities->type($this->view), 'application/json+activities');
	}

	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = json_decode($doc, true);
		$this->assertNotNull($data);
		$item = $data['activities'][0];
		$this->assertNotNull($item);
		$activity = $item['activity'];
		$this->assertNotNull($activity);

		foreach($this->fields as $key => $value)
			$this->assertEqual($activity[$key], $value['value']);
	}
}

class TestOfYAMLAcivitiesClass extends TestOfactivitiesView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testEmptyDocument() {
		$this->assertNotNull($this->activities->generateDocument($this->view), '');
	}

	function testDocumentType() {
		$this->assertEqual($this->activities->type($this->view), 'text/x-yaml+activities');
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$data = yaml_parse($doc);
		$this->assertNotNull($data);
		$item = $data['activities'][0];
		$this->assertNotNull($item);
		$activity = $item['activity'];
		$this->assertNotNull($activity);

		foreach($this->fields as $key => $value)
			$this->assertEqual($activity[$key], $value['value']);
	}
}

?>
