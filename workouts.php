<?php

require_once 'interfaces.php';
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

	public function delete(){
		// add workout to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Workouts WHERE User_ID = '" . $this->user_id . "'");
	}

	public function location(){
		return '';
	}
	
}

class xmlWorkouts implements View {

	private $type = 'workouts+xml';
	private $writer;

	public function xmlWorkouts(){		
	}

	public function parse($data, $workout){
		//check mime type
		$this->data = new SimpleXMLElement($data);
		//parse xml into values
		$workout->workout_id = $this->data->workout_id;
		$workout->username = $this->data->username;
		$workout->user_id = $this->data->user_id;
		$workout->activity = $this->data->activity;
		$workout->start = $this->data->weight;
		$workout->duration = $this->data->duration;
		$workout->kcal = $this->data->kcal;
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

	public function addElements($workouts, $writer){
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
?>
