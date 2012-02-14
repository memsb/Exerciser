<?php

require_once dirname(__FILE__) . '/../lib/interfaces.php';
require_once 'workout.php';

class Workouts extends CRUD {

	protected $user_id;
	protected $workouts = array();

	public function Workouts($uri){
		$this->uri = $uri;
	}

	public function setDate($date){
		$this->from = strtotime($date);
		$this->to = strtotime('+1 day', $this->from);
	}

	public function setPeriod($from, $to){
		$this->from = strtotime($from);
		$this->to = strtotime($to);
	}

	public function setActivity($activity){
		$this->activity = $activity;
	}

	public function load($user_id){
		$this->user_id = $user_id;
		// load in stats from database
		$db = new Database();
		$db->connect();
		$params = '';
		
		if( isset($this->from) && isset($this->to) )
			$params .= ' AND UNIX_TIMESTAMP(Start_Time) BETWEEN ' . 
					mysql_real_escape_string($this->from) . 
					' AND ' . 
					mysql_real_escape_string($this->to);
		if( isset($this->activity) )
			$params .= ' AND Activity_ID = ' . mysql_real_escape_string($this->activity);

		$result = $db->query("SELECT Workout_ID 
					FROM Workouts 
					WHERE User_ID = '" . mysql_real_escape_string($user_id) . "'" . $params);
		if( ! $result )
			return;

		while($row = mysql_fetch_array($result)){
			$workout = new Workout($this->uri . '/' . $row['Workout_ID']);
			$workout->load($row['Workout_ID']);
			$this->workouts[] = $workout;
		}
	}

	public function delete(){
		// add workout to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Workouts WHERE User_ID = '" . mysql_real_escape_string($this->user_id) . "'");
	}	
}

class xmlWorkouts implements View {

	private $type = 'application/xml+workouts';
	private $writer;

	public function xmlWorkouts(){		
	}

	public function parse($data, $workouts){	
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

	private $type = 'application/json+workouts';
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

	private $type = 'text/x-yaml+workouts';
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
		$view = new yamlWorkout();
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
