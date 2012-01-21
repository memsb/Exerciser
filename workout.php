<?php

require_once 'interfaces.php';

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
		// load in stats from database
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT 	Workout_ID, 
						Users.Username AS Username,
						Users.User_ID AS User_ID,
						Activities.Activity_ID AS Activity_ID,
						Start_Time, 
						Duration, 
						Kcal
						FROM Workouts 
						JOIN Users USING (User_ID)
						JOIN Activities USING (Activity_ID)
						WHERE Workout_ID = '" . $workoutID . "'"
					);
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
		// add user to database
		$db = new Database();
		$db->connect();
		$result = $db->query("INSERT INTO Workouts (	User_ID, 
								Activity_ID, 
								Start_Time, 
								Duration, 
								Kcal
								) VALUES ('" . 
								$this->user_id . "', '" . 
								$this->activity_id . "', '" . 
								$this->start . "', '" . 
								$this->duration . "', '" . 
								$this->kcal . "')"
					);
	}

	public function update(){
		$db = new Database();
		$db->connect();
		$result = $db->query("UPDATE Workouts SET 
							Activity_ID = 	'" . $this->activity_id . "',
							Start_Time = 	'" . strtotime($this->start) . "',
							Duration = 	'" . $this->duration . "',
							Kcal = 		'" . $this->kcal . "'
					WHERE Workout_ID = '" . $this->workout_id . "'"
					);
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

	public function addElements($workout, $writer){
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
?>
