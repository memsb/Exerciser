<?php

require_once dirname(__FILE__) . '/../lib/interfaces.php';

class Workout extends CRUD {

	protected $workout_id;
	protected $username;
	protected $user_id;
	protected $activity_id;
	protected $start;
	protected $duration;
	protected $kcal;

	public function Workout(){
	}

	public function load($workoutID){
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT 	Workout_ID, 
						Users.Username AS Username,
						Users.User_ID AS User_ID,
						Activities.Activity_ID AS Activity_ID,
						UNIX_TIMESTAMP(Start_Time) AS Start_Time, 
						Duration, 
						Kcal
						FROM Workouts 
						JOIN Users USING (User_ID)
						JOIN Activities USING (Activity_ID)
						WHERE Workout_ID = '" . $workoutID . "'"
					);
		if( mysql_num_rows($result) == 0 ){
			throw new Exception("No matching entry in database");
		}
		$row = mysql_fetch_array($result);
		$this->workout_id = $row['Workout_ID'];
		$this->username = $row['Username'];
		$this->user_id = $row['User_ID'];
		$this->activity_id = $row['Activity_ID'];
		$this->start = $row['Start_Time'];
		$this->duration = $row['Duration'];
		$this->kcal = $row['Kcal'];
	}

	public function create(){
		$db = new Database();
		$db->connect();
		$result = $db->query("INSERT INTO Workouts (	User_ID, 
								Activity_ID, 
								UNIX_TIMESTAMP(Start_Time), 
								Duration, 
								Kcal
								) VALUES ('" . 
								$this->user_id . "', '" . 
								$this->activity_id . "', '" . 
								$this->start . "', '" . 
								$this->duration . "', '" . 
								$this->kcal . "')"
					);
		$this->workout_id = mysql_insert_id();
	}

	public function update(){
		$db = new Database();
		$db->connect();
		$result = $db->query("UPDATE Workouts SET 
							Activity_ID = 	'" . $this->activity_id . "',
							Start_Time = 	UNIX_TIMESTAMP('" . strtotime($this->start) . "'),
							Duration = 	'" . $this->duration . "',
							Kcal = 		'" . $this->kcal . "'
					WHERE Workout_ID = '" . $this->workout_id . "'"
					);
		if( !$result ){
			throw new Exception("No matching entry in database");
		}
	}

	public function delete(){
		// add workout to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Workouts WHERE Workout_ID = '" . $this->workout_id . "'");
	}

	public function location(){
		return $this->workout_id;
	}
	
}

class xmlWorkout implements View {

	private $type = 'workout+xml';
	private $writer;

	public function xmlWorkout(){		
	}

	public function parse($data, $workout){
		//check mime type
		$this->data = new SimpleXMLElement($data);
		//parse xml into values
		$workout->workout_id = $this->data->workout_id;
		$workout->username = $this->data->username;
		$workout->user_id = $this->data->user_id;
		$workout->activity_id = $this->data->activity_id;
		$workout->start = $this->data->weight;
		$workout->duration = $this->data->duration;
		$workout->kcal = $this->data->kcal;
	}

	public function generateDocument($workout){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($workout, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($workout, &$writer){
		$writer->startElement('workout');

		$writer->startElement('workout_id');
		$writer->text($workout->workout_id);
		$writer->endElement();
	
		$writer->startElement('username');
		$writer->text($workout->username);
		$writer->endElement();

		$writer->startElement('user_id');
		$writer->text($workout->user_id);
		$writer->endElement();

		$writer->startElement('activity_id');
		$writer->text($workout->activity_id);
		$writer->endElement();

		$writer->startElement('start');
		$writer->text($workout->start);
		$writer->endElement();

		$writer->startElement('duration');
		$writer->text($workout->duration);
		$writer->endElement();

		$writer->startElement('kcal');
		$writer->text($workout->kcal);
		$writer->endElement();

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

class jsonWorkout implements View {

	private $type = 'workout+json';
	private $data;

	public function jsonWorkout(){
	}

	public function parse($data, $workout){	
		$this->data = json_decode($data, true);
		$workout_data = $this->data['workout'];
		$workout->workout_id = $workout_data['workout_id'];
		$workout->username = $workout_data['username'];
		$workout->user_id = $workout_data['user_id'];
		$workout->activity_id = $workout_data['activity_id'];
		$workout->start = $workout_data['start'];
		$workout->duration = $workout_data['duration'];
		$workout->kcal = $workout_data['kcal'];
	}

	public function generateDocument($workout){
		$this->data = array();
		$this->addElements($workout, $this->data);
	}

	public function addElements($workout, &$data){
		$data = array('workout' => 
				array(
					'workout_id' => $workout->workout_id,
					'username' => $workout->username,
					'user_id' => $workout->user_id,
					'activity_id' => $workout->activity_id,
					'start' => $workout->start,
					'duration' => $workout->duration,
					'kcal' => $workout->kcal
				)
			);
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return json_encode($this->data);
	}
}

class yamlWorkout implements View {

	private $type = 'workout+yaml';
	private $data;

	public function yamlWorkout(){
	}

	public function parse($data, $workout){	
		$this->data = yaml_parse($data);
		$workout_data = $this->data['workout'];
		$workout->workout_id = $workout_data['workout_id'];
		$workout->username = $workout_data['username'];
		$workout->user_id = $workout_data['user_id'];
		$workout->activity_id = $workout_data['activity_id'];
		$workout->start = $workout_data['start'];
		$workout->duration = $workout_data['duration'];
		$workout->kcal = $workout_data['kcal'];
	}

	public function generateDocument($workout){
		$this->addElements($workout, $this->data);
	}

	public function addElements($workout, &$data){
		$data = array('workout' => 
				array(
					'workout_id' => $workout->workout_id,
					'username' => $workout->username,
					'user_id' => $workout->user_id,
					'activity_id' => $workout->activity_id,
					'start' => $workout->start,
					'duration' => $workout->duration,
					'kcal' => $workout->kcal
				)
			);
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return yaml_emit($this->data);
	}
}
?>
