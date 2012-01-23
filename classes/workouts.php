<?php

require_once dirname(__FILE__) . '/../lib/interfaces.php';
require_once 'workout.php';

class Workouts extends CRUD {

	protected $user_id;
	protected $workouts = array();

	public function Workouts(){
	}

	public function load($user_id){
		$this->user_id = $user_id;
		// load in stats from database
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT Workout_ID FROM Workouts WHERE User_ID = '" . $user_id . "'");
		while($row = mysql_fetch_array($result)){
			$workout = new Workout();
			$workout->load($row['Workout_ID']);
			$this->workouts[] = $workout;
		}
	}

	public function create(){
		$this->workouts->create();
	}

	public function delete(){
		// add workout to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Workouts WHERE User_ID = '" . $this->user_id . "'");
	}

	public function location(){
		return $this->user_id . '/workouts';
	}
	
}

class xmlWorkouts implements View {

	private $type = 'workouts+xml';
	private $writer;

	public function xmlWorkouts(){		
	}

	public function parse($data, $workouts){
		$workout = new xmlWorkout();
		$workout->parse($data, $workouts);
		$workouts->workouts = $workout;		
	}

	public function generateDocument($workouts){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($workouts, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($workouts, &$writer){
		$writer->startElement('workouts');

		foreach($workouts->workouts as $workout){
			$view = new xmlWorkout();
			$view->addElements($workout, $writer);
		}

		$writer->endElement();
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		if($this->writer != null){		
			return $this->writer->outputMemory();
		}else{
			return '';
		}
	}
}

class jsonWorkouts implements View {

	private $type = 'workouts+json';
	private $writer;

	public function jsonWorkouts(){		
	}

	public function parse($data, $workouts){
	}

	public function generateDocument($workouts){
		$data = array();
		$this->addElements($workouts, $data);
		$this->data['workouts'] = $data;
	}

	public function addElements($workouts, &$data){
		$view = new jsonWorkout();
		foreach($workouts->workouts as $workout){			
			$view->addElements($workout, $entry);
			$data[] = $entry;
		}
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return json_encode($this->data);
	}
}

class yamlWorkouts implements View {

	private $type = 'workouts+yaml';
	private $writer;

	public function yamlWorkouts(){		
	}

	public function parse($data, $workout){
	}

	public function generateDocument($workouts){
		$data = array();
		$this->addElements($workouts, $data);
		$this->data['workouts'] = $data;
	}

	public function addElements($workouts, &$data){
		$view = new jsonWorkout();
		foreach($workouts->workouts as $workout){			
			$view->addElements($workout, $entry);
			$data[] = $entry;
		}
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return yaml_emit($this->data);
	}
}
?>
