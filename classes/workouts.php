<?php

require_once LIB . 'CRUD.php';
require_once CLASSES . 'workout.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Workouts class is a model class containing a list of Workout
 *	Workouts can be loaded on a per user, activity or time period basis
 * 	@namespace Exerciser
 */
class Workouts extends CRUD {

	protected $type = 'workouts';
	protected $user_id;
	protected $workouts = array();

	/**
	 * Constructor calls constructor in CRUD
	 * @param Database instance to be used
	 */
	public function Workouts($db){
		parent::__construct($db);
	}

	/**
	 * Sets the date on which to load Workouts from
	 * @param Int datetime
	 */
	public function setDate($date){
		$this->from = strtotime($date);
		$this->to = strtotime('+1 day', $this->from);
	}

	/**
	 * Sets the time period to load workouts from
	 * @param Int datetime to load from
	 * @param Int datetime to load to
	 */
	public function setPeriod($from, $to){
		$this->from = strtotime($from);
		$this->to = strtotime($to);
	}

	/**
	 * Sets the activity type to load
	 * @param Activity
	 */
	public function setActivity($activity){
		$this->activity = $activity;
	}

	/**
	 * Gives the list of loaded Workout
	 * @return Array of Workout
	 */
	public function getWorkouts(){
		return $this->workouts;
	}

	/**
	 * Gives a count of the number of loaded Workout
	 * @return Int the number of loaded Workout
	 */
	public function size(){
		return count($this->workouts);
	}

	/**
	 * Retrives all relevant Workout from the database
	 * @param Int the users ID number
	 */
	public function retrieve($user_id){
		$this->user_id = $user_id;
		$id = $this->db->clean($this->user_id);
		$params = '';
		
		if( isset($this->from) && isset($this->to) ){
			$from = $this->db->clean($this->from);
			$to = $this->db->clean($this->to);
			$params .= " AND UNIX_TIMESTAMP(Start_Time) BETWEEN '$from' AND '$to' ";
		}
		if( isset($this->activity) ){
			$activity = $this->db->clean($this->activity);
			$params .= " AND Activity_ID = '$activity'";
		}

		$result = $this->db->query("SELECT Workout_ID FROM Workouts WHERE User_ID = '$id'" . $params);
		return $result;
	}

	/**
	 * Adds a Workout the the Workouts list
	 * @param Workout to be added
	 */
	public function addWorkout(Workout $workout){
		$this->workouts[] = $workout;
	}

	/**
	 * Loads all Workout from database
	 * @param Int the user ID
	 */
	public function load($user_id){
		$result = $this->retrieve($user_id);
		foreach($result as $row){
			$workout = new Workout($this->db);
			$workout->setLocation($this->uri . '/' . $row['Workout_ID']);
			$workout->load($row['Workout_ID']);
			$this->addWorkout( $workout );
		}
	}

	/**
	 * Deletes all Workout for selected User from Database
	 */
	public function delete(){
		$id = $this->db->clean($this->user_id);
		$result = $this->db->query("DELETE FROM Workouts WHERE User_ID = '$id'");
		if( ! $result )
			throw new Exception("Workouts not deleted.");
		return True;
	}

	/**
	 * Generates a document representation of the model in the requested view
	 * @param View type requested
	 * @return String containing the document
	 */
	public function generateDocument($view){
		$view->startDocument();
		$view->startElement('workouts');
		$i = 0;
		foreach($this->workouts as $workout){
			$view->startElement($i++);		
			$workout->addProperties($view);
			$view->endElement();
		}

		$view->endElement();
		return $view->toString();
	}

}
?>
