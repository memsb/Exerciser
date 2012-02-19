<?php

require_once('/var/www/exerciser/classes/stats.php');
require_once('/var/www/exerciser/lib/DBA.php');

class TestOfStatsClass extends UnitTestCase {

	private $uri = 'uri';
	private $db;
	private $stats;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->stats = new Stats($this->db);
	}

	function testResourceLocation() {		
		$this->assertEqual($this->stats->location(), '');
		$this->stats->setLocation($this->uri);
		$this->assertEqual($this->stats->location(), $this->uri);
	}

	function testNoDatabase() {
		$this->expectException('BadMethodCallException');		
		$this->stats = new Stats(NULL);
	}

	function testLoad() {	
		$this->stats->load();	
		$this->assertNotNull($this->stats->user_count);
		$this->assertNotNull($this->stats->longest_workout);
		$this->assertNotNull($this->stats->biggest_workout);
		$this->assertNotNull($this->stats->popular_activity);
		$this->assertNotNull($this->stats->most_kcal);	
	}

}


class TestOfStatsView extends UnitTestCase {

	protected $workout_data = array(
				'workout_id' => array('value' => 2, 'default' => 0),
				'user_id' => array('value' => 1, 'default' => 0), 
				'username' => array('value' => 'username', 'default' => ''),
				'activity_id' => array('value' => 3, 'default' => 0),
				'activity_name' => array('value' => 'activity_name', 'default' => ''), 
				'start' => array('value' => 123, 'default' => 0),
				'duration' => array('value' => 20, 'default' => 0), 
				'kcal' => array('value' => 123, 'default' => 0)
				);

	protected $activity_data = array(
				'activity_id' => array('value' => 3, 'default' => 0), 
				'activity_name' => array('value' => 'activity', 'default' => ''), 
				'description' => array('value' => 'description', 'default' => ''), 
				'kcal_hour' => array('value' => 123, 'default' => 0)
				);

	protected $user_data = array(
				'user_id' => array('value' => 1, 'default' => 0), 
				'username' => array('value' => 'username', 'default' => ''), 
				'age' => array('value' => 3, 'default' => 0), 
				'weight' => array('value' => 80.0, 'default' => 0.0), 
				'gender' => array('value' => 'Female', 'default' => 'Male')
				);

	protected $user_count = 100;

	protected $view;
	protected $db;
	protected $stats;

	function setup(){
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');		
		$this->stats = new Stats($this->db);
	}

	function testParseEmptyDocument() {		
		$this->expectException('BadMethodCallException');
		$this->stats->parse(NULL, NULL);
	}

	protected function createDoc(){
		$workout = new Workout($this->db);
		foreach($this->workout_data as $key => $value)
			$workout->$key = $value['value'];

		$activity = new Activity($this->db);
		foreach($this->activity_data as $key => $value)
			$activity->$key = $value['value'];

		$user = new User($this->db);
		foreach($this->user_data as $key => $value)
			$user->$key = $value['value'];

		$this->stats->user_count = $this->user_count;
		$this->stats->longest_workout = $workout;
		$this->stats->biggest_workout = $workout;
		$this->stats->popular_activity = $activity;
		$this->stats->most_kcal = $user;

		$doc = $this->stats->generateDocument($this->view);
		$this->assertNotNull($doc);
		return $doc;
	}

	protected function EmptyDocument() {
		$this->assertNotNull($this->stats->generateDocument($this->view));
	}

	protected function ParseEmptyDocument() {		
		$this->expectException('Exception');
		$this->stats->parse(NULL, $this->view);
	}
}

class TestOfXMLStatsClass extends TestOfStatsView {

	function setup(){
		parent::setup();
		$this->view = new XML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->stats->type($this->view), 'application/xml+stats');
	}

	function testEmptyDocument() {
		parent::EmptyDocument();
	}

	function testParseEmptyDocument() {		
		parent::ParseEmptyDocument();
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc($this->user_data);

		$data = simplexml_load_string($doc);
		$this->assertNotNull($data);

		$user_count = $data->user_count;
		$this->assertEqual($user_count, $this->user_count);

		$biggest_workout = (array)$data->biggest_workout->workout;		
		foreach($this->workout_data as $key => $value)
			$this->assertEqual($biggest_workout[$key], $value['value']);

		$longest_workout = (array)$data->longest_workout->workout;
		foreach($this->workout_data as $key => $value)
			$this->assertEqual($longest_workout[$key], $value['value']);

		$popular_activity = (array)$data->popular_activity->activity;
		foreach($this->activity_data as $key => $value)
			$this->assertEqual($popular_activity[$key], $value['value']);

		$most_kcal = (array)$data->most_kcal->user;
		foreach($this->user_data as $key => $value)
			$this->assertEqual($most_kcal[$key], $value['value']);
		
	}
}

class TestOfJSONStatsClass extends TestOfStatsView {

	function setup(){
		parent::setup();
		$this->view = new JSON();		
	}

	function testDocumentType() {
		$this->assertEqual($this->stats->type($this->view), 'application/json+stats');
	}

	function testEmptyDocument() {
		parent::EmptyDocument();
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc();

		$item = json_decode($doc, true);
		$this->assertNotNull($item);
		$data = $item['statistics'];

		$user_count = $data['user_count'];
		$this->assertEqual($user_count, $this->user_count);

		$biggest_workout = $data['biggest_workout']['workout'];		
		foreach($this->workout_data as $key => $value)
			$this->assertEqual($biggest_workout[$key], $value['value']);

		$longest_workout = $data['longest_workout']['workout'];
		foreach($this->workout_data as $key => $value)
			$this->assertEqual($longest_workout[$key], $value['value']);

		$popular_activity = $data['popular_activity']['activity'];
		foreach($this->activity_data as $key => $value)
			$this->assertEqual($popular_activity[$key], $value['value']);

		$most_kcal = $data['most_kcal']['user'];
		foreach($this->user_data as $key => $value)
			$this->assertEqual($most_kcal[$key], $value['value']);
	}
}


class TestOfYAMLStatsClass extends TestOfStatsView {

	function setup(){
		parent::setup();
		$this->view = new YAML();		
	}

	function testDocumentType() {
		$this->assertEqual($this->stats->type($this->view), 'text/x-yaml+stats');
	}

	function testEmptyDocument() {
		parent::EmptyDocument();
	}
	
	function testGenerateDocument() {
		$doc = $this->createDoc($this->user_data);

		$item = yaml_parse($doc);
		$this->assertNotNull($item);
		$data = $item['statistics'];

		$user_count = $data['user_count'];
		$this->assertEqual($user_count, $this->user_count);

		$biggest_workout = $data['biggest_workout']['workout'];		
		foreach($this->workout_data as $key => $value)
			$this->assertEqual($biggest_workout[$key], $value['value']);

		$longest_workout = $data['longest_workout']['workout'];
		foreach($this->workout_data as $key => $value)
			$this->assertEqual($longest_workout[$key], $value['value']);

		$popular_activity = $data['popular_activity']['activity'];
		foreach($this->activity_data as $key => $value)
			$this->assertEqual($popular_activity[$key], $value['value']);

		$most_kcal = $data['most_kcal']['user'];
		foreach($this->user_data as $key => $value)
			$this->assertEqual($most_kcal[$key], $value['value']);
	}
}

?>
