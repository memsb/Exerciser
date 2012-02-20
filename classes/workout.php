<?php

require_once LIB . 'CRUD.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Workout handles details of a Workout
 * 	@namespace Exerciser
 */
class Workout extends CRUD {

	protected $type = 'workout';

	protected $workout_id = 0;
	protected $username = '';
	protected $user_id = 0;
	protected $activity_id = 0;
	protected $activity_name = '';
	protected $start = 0;
	protected $duration = 0;
	protected $kcal = 0;

	protected $required = array('user_id', 'activity_id', 'start', 'duration', 'kcal');

	/**
	 * Constructor calls constructor in CRUD
	 * @param Database instance to be used
	 */
	public function __construct($db){
		parent::__construct($db);
	}

	/**
	 * Loads the details of the specified Workout
	 * @param Int the Workout ID
	 */
	public function load($workoutID){
		$id = $this->db->clean($workoutID);
		$result = $this->db->query("SELECT 	Users.Username AS Username,
							Users.User_ID AS User_ID,
							Activities.Activity_ID AS Activity_ID,
							Activities.Activity_Name AS Activity_Name,
							UNIX_TIMESTAMP(Start_Time) AS Start_Time, 
							Duration, 
							Kcal
							FROM Workouts 
							JOIN Users USING (User_ID)
							JOIN Activities USING (Activity_ID)
							WHERE Workout_ID = '$id'"
					);
		if( ! $result || count($result) == 0 )
			throw new LengthException("Workout not found.");
		
		$row = $result[0];
		$this->workout_id = $id;
		$this->username = $row['Username'];
		$this->user_id = $row['User_ID'];
		$this->activity_id = $row['Activity_ID'];
		$this->activity_name = $row['Activity_Name'];
		$this->start = $row['Start_Time'];
		$this->duration = $row['Duration'];
		$this->kcal = $row['Kcal'];
	}

	/**
	 * Creates a new workout using the current details
	 * @return Boolean True on success
	 */
	public function create(){
		$this->validate();
		$user_id = $this->db->clean($this->user_id);
		$activity_id = $this->db->clean($this->activity_id);
		$start = $this->db->clean(strtotime($this->start));
		$duration = $this->db->clean($this->duration);
		$kcal = $this->db->clean($this->kcal);		
		$result = $this->db->query("INSERT INTO Workouts (User_ID, Activity_ID, UNIX_TIMESTAMP(Start_Time), Duration, Kcal
								) VALUES ('$user_id', '$activity_id', '$start', '$duration', '$kcal')");

		if( ! $result )
			throw new Exception("Workout not created.");

		$this->workout_id = $this->db->insert_id();
		$this->uri .= '/' . $this->workout_id;
		return True;
	}

	/**
	 * Updates the current workout
	 * @return Boolean True on success
	 */
	public function update(){
		$this->validate();
		$activity_id = $this->db->clean($this->activity_id);
		$start = $this->db->clean(strtotime($this->start));
		$duration = $this->db->clean($this->duration);
		$kcal = $this->db->clean($this->kcal);
		$workout_id = $this->db->clean($this->workout_id);
		$result = $this->db->query("UPDATE Workouts SET 
						Activity_ID = 	'$activity_id',
						Start_Time = 	UNIX_TIMESTAMP('$start'),
						Duration = 	'$duration',
						Kcal = 		'$kcal'
						WHERE Workout_ID = '$workout_id'"
					);
		if( ! $result )
			throw new Exception("Workout not updated.");
		return True;
	}

	/**
	 * Deletes the current workout
	 * @return Boolean True on success
	 */
	public function delete(){
		$id = $this->db->clean($this->workout_id);
		$result = $this->db->query("DELETE FROM Workouts WHERE Workout_ID = '$id'");
		if(  ! $result )
			throw new Exception("Workout not deleted.");

		return True;
	}

	/**
	 * Iterates through the required values to check they are specified
	 */
	public function validate(){
		foreach( $this->required as $field )
			if( !$this->$field )
				throw new Exception("Data for field: $field required but not present");
	}

	/**
	 * Utilises the View specified to read data from the document
	 * @param String containing the document 
	 * @param View specifying the document type
	 */
	public function parse($data_str, $view){
		if( ! $view )
			throw new BadMethodCallException('Function not implemented.');

		try{
			$view->parse($data_str);
			$data = $view->getArray();
			$workout = $data['workout'];

			foreach( $this->required as $field )
				$this->$field = $workout[$field];
		}catch(Exception $e){
			throw new UnexpectedValueException($e->getMessage());
		}
	}

	/**
	 * Adds elements to the specified View
	 * @param View specifying the document type
	 */
	public function addProperties($view){
		$view->startElement('workout');

		$view->startElement('workout_id');
		$view->text($this->workout_id);
		$view->endElement();
	
		$view->startElement('username');
		$view->text($this->username);
		$view->endElement();

		$view->startElement('user_id');
		$view->text($this->user_id);
		$view->endElement();

		$view->startElement('activity_id');
		$view->text($this->activity_id);
		$view->endElement();

		$view->startElement('activity_name');
		$view->text($this->activity_name);
		$view->endElement();

		$view->startElement('start');
		$view->text($this->start);
		$view->endElement();

		$view->startElement('duration');
		$view->text($this->duration);
		$view->endElement();

		$view->startElement('kcal');
		$view->text($this->kcal);
		$view->endElement();

		$view->endElement();
	}	
}

?>
