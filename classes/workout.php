<?php

require_once dirname(__FILE__) . '/../lib/interfaces.php';

class Workout extends CRUD {

	protected $workout_id;
	protected $username;
	protected $user_id;
	protected $activity_id;
	protected $activity_name;
	protected $start;
	protected $duration;
	protected $kcal;

	protected $required = array('user_id', 'activity_id', 'start_time', 'duration', 'kcal');

	public function Workout($uri){
		$this->uri = $uri;
	}

	public function load($workoutID){
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT 	Users.Username AS Username,
						Users.User_ID AS User_ID,
						Activities.Activity_ID AS Activity_ID,
						Activities.Activity_Name AS Activity_Name,
						UNIX_TIMESTAMP(Start_Time) AS Start_Time, 
						Duration, 
						Kcal
						FROM Workouts 
						JOIN Users USING (User_ID)
						JOIN Activities USING (Activity_ID)
						WHERE Workout_ID = '" . mysql_real_escape_string($workoutID) . "'"
					);
		if( mysql_num_rows($result) == 0 ){
			throw new Exception("No matching entry in database");
		}
		$row = mysql_fetch_array($result);
		$this->workout_id = $workoutID;
		$this->username = $row['Username'];
		$this->user_id = $row['User_ID'];
		$this->activity_id = $row['Activity_ID'];
		$this->activity_name = $row['Activity_Name'];
		$this->start = $row['Start_Time'];
		$this->duration = $row['Duration'];
		$this->kcal = $row['Kcal'];
	}

	public function create(){
		$this->validate();
		$db = new Database();
		$db->connect();
		$result = $db->query("INSERT INTO Workouts (	User_ID, 
								Activity_ID, 
								UNIX_TIMESTAMP(Start_Time), 
								Duration, 
								Kcal
								) VALUES ('" . 
								mysql_real_escape_string($this->user_id) . "', '" . 
								mysql_real_escape_string($this->activity_id) . "', '" . 
								mysql_real_escape_string($this->start) . "', '" . 
								mysql_real_escape_string($this->duration) . "', '" . 
								mysql_real_escape_string($this->kcal) . "')"
					);
		$this->workout_id = mysql_insert_id();
		$this->uri .= '/' . $this->workout_id;
	}

	public function update(){
		$this->validate();
		$db = new Database();
		$db->connect();
		$result = $db->query("UPDATE Workouts SET 
						Activity_ID = 	'" . mysql_real_escape_string($this->activity_id) . "',
						Start_Time = 	UNIX_TIMESTAMP('" . strtotime($this->start) . "'),
						Duration = 	'" . mysql_real_escape_string($this->duration) . "',
						Kcal = 		'" . mysql_real_escape_string($this->kcal) . "'
						WHERE Workout_ID = '" . mysql_real_escape_string($this->workout_id) . "'"
					);
		if( !$result ){
			throw new Exception("No matching entry in database");
		}
	}

	public function delete(){
		// add workout to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Workouts WHERE Workout_ID = '" . mysql_real_escape_string($this->workout_id) . "'");
	}

	public function validate(){
		foreach( $this->required as $field )
			if( !$this->$field )
				throw new UnexpectedValueException("Data for field: $field required but not present");
	}

	public function location(){
		return $this->uri;
	}
	
}

class xmlWorkout implements View {

	private $type = 'application/xml+workout';
	private $writer;

	public function xmlWorkout(){		
	}

	public function parse($data, $workout){
		//check mime type
		$this->data = new SimpleXMLElement($data);
		//parse xml into values
		foreach( $workout->required as $field )
			$workout->$field = $this->data->$field;
		$workout->validate();
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
		$writer->writeAttribute('uri', $workout->location());

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

		$writer->startElement('activity_name');
		$writer->text($workout->activity_name);
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

	private $type = 'application/json+workout';
	private $data;

	public function jsonWorkout(){
	}

	public function parse($data, $workout){	
		$this->data = json_decode($data, true);
		foreach( $workout->required as $field )
			$workout->$field = $this->data['workout'][$field];
		$workout->validate();
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
					'activity_name' => $workout->activity_name,
					'start' => $workout->start,
					'duration' => $workout->duration,
					'kcal' => $workout->kcal
				),
				'uri' => $workout->location() . '.json'
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

	private $type = 'text/x-yaml+workout';
	private $data;

	public function yamlWorkout(){
	}

	public function parse($data, $workout){	
		$this->data = yaml_parse($data);
		foreach( $workout->required as $field )
			$workout->$field = $this->data['workout'][$field];
		$workout->validate();
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
					'activity_name' => $workout->activity_name,
					'start' => $workout->start,
					'duration' => $workout->duration,
					'kcal' => $workout->kcal
				),
				'uri' => $workout->location() . '.yaml'
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
