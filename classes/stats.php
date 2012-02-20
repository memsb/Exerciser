<?php

require_once LIB . 'CRUD.php';
require_once CLASSES . 'user.php';
require_once CLASSES . 'workout.php';
require_once CLASSES . 'activity.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Stats generates system usage statistics
 * 	@namespace Exerciser
 */
class Stats extends CRUD {

	protected $type = 'stats';

	protected $user_count = 0;
	protected $longest_workout;
	protected $biggest_workout;
	protected $popular_workout;
	protected $most_kcal;

	/**
	 * Constructor calls constructor in CRUD
	 * Creates attributes with default values
	 * @param Database instance to be used
	 */
	public function Stats($db){
		parent::__construct($db);

		$this->longest_workout = new Workout($this->db);
		$this->biggest_workout = new Workout($this->db);
		$this->popular_activity = new Activity($this->db);
		$this->most_kcal = new User($this->db);
	}

	/**
	 * Loads in the Stats from the Database
	 */
	public function load(){
		$this->user_count = $this->countUsers();
		$this->longest_workout = $this->longestWorkout();
		$this->biggest_workout = $this->biggestWorkout();
		$this->popular_activity = $this->popularActivity();
		$this->most_kcal = $this->most_kcal();
	}

	/**
	 * Loads the total number of users in the system
	 */
	private function countUsers(){
		$result = $this->db->query('SELECT COUNT(*) AS user_count FROM Users');
		if( ! $result || count($result) == 0 )
			return 0;
		$row = $result[0];
		return $row['user_count'];
	}

	/**
	 * Loads details on the longest Workout in the system. Greatest Duration
	 */
	private function longestWorkout(){
		$workout = new Workout($this->db);
		$result = $this->db->query('SELECT Workout_ID, User_ID FROM Workouts
					ORDER BY Duration DESC LIMIT 0,1');

		if( ! $result || count($result) == 0 )
			return $workout;

		$row = $result[0];		
		$workout->setLocation('/' . $row['User_ID'] . '/workouts/' . $row['Workout_ID']);
		$workout->load($row['Workout_ID']);
		return $workout;
	}

	/**
	 * Loads details on the biggest Workout in the system. Most Kcal
	 */
	private function biggestWorkout(){
		$workout = new Workout($this->db);
		$result = $this->db->query('SELECT Workout_ID, User_ID FROM Workouts
					ORDER BY Kcal DESC LIMIT 0,1');

		if( ! $result || count($result) == 0 )
			return $workout;

		$row = $result[0];		
		$workout->setLocation('/' . $row['User_ID'] . '/workouts/' . $row['Workout_ID']);
		$workout->load($row['Workout_ID']);
		return $workout;
	}

	/**
	 * Loads details on the most popular Activity.
	 */
	private function popularActivity(){
		$activity = new Activity($this->db);
		$result = $this->db->query('SELECT Activities.*, COUNT(Activity_ID) AS num FROM Activities 
					JOIN Workouts USING (Activity_ID) 
					GROUP BY Activity_ID ORDER BY num DESC LIMIT 0,1');

		if( ! $result || count($result) == 0 )
			return $activity;

		$row = $result[0];		
		$activity->setLocation('/activities/' . $row['Activity_ID']);
		$activity->load($row['Activity_ID']);
		return $activity;
	}

	/**
	 * Loads details on the user who has burned the most Kcal
	 */
	private function most_kcal(){
		$user = new User($this->db);
		$result = $this->db->query('SELECT User_ID, SUM(Kcal) AS Kcal FROM Workouts GROUP BY User_ID 
					ORDER BY Kcal DESC LIMIT 0,1');

		if( ! $result || count($result) == 0 )
			return $user;

		$row = $result[0];		
		$user->setLocation('/' . $row['User_ID']);
		$user->load($row['User_ID']);
		return $user;
	}

	/**
	 * Adds the elements to the specified View
	 * @param View to add the elements to
	 */
	public function addProperties($view){
		$view->startElement('statistics');
		
		$view->startElement('user_count');
		$view->text($this->user_count);
		$view->endElement();

		$view->startElement('longest_workout');
		$this->longest_workout->addProperties($view);
		$view->endElement();

		$view->startElement('biggest_workout');
		$this->biggest_workout->addProperties($view);
		$view->endElement();

		$view->startElement('popular_activity');
		$this->popular_activity->addProperties($view);
		$view->endElement();

		$view->startElement('most_kcal');
		$this->most_kcal->addProperties($view);
		$view->endElement();

		$view->endElement();
	}
}

?>
