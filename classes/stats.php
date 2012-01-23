<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';
require_once 'user.php';
require_once 'workout.php';
require_once 'activity.php';

class Stats extends CRUD {

	//stat values
	protected $user_count;
	protected $longest_workout;
	protected $biggest_workout;
	protected $popular_workout;
	protected $most_kcal;

	public function Stats(){
	}

	public function load(){
		// load in stats from database
		$db = new Database();
		$db->connect();

		$this->user_count = $this->countUsers($db);
		$this->longest_workout = $this->longestWorkout($db);
		$this->biggest_workout = $this->biggestWorkout($db);
		$this->popular_activity = $this->popularActivity($db);
		$this->most_kcal = $this->most_kcal($db);
	}

	private function countUsers($db){
		$result = $db->query('SELECT COUNT(*) AS user_count FROM Users');
		$row = mysql_fetch_array($result);
		return $row['user_count'];
	}

	private function longestWorkout($db){
		$result = $db->query('SELECT Workout_ID FROM Workouts
					ORDER BY Duration DESC LIMIT 0,1');
		$row = mysql_fetch_array($result);
		$workout = new Workout();
		$workout->load($row['Workout_ID']);
		return $workout;
	}

	private function biggestWorkout($db){
		$result = $db->query('SELECT Workout_ID FROM Workouts
					ORDER BY Kcal DESC LIMIT 0,1');
		$row = mysql_fetch_array($result);
		$workout = new Workout();
		$workout->load($row['Workout_ID']);
		return $workout;
	}

	private function popularActivity($db){
		$result = $db->query('SELECT Activities.*, COUNT(Activity_ID) AS num FROM Activities 
					JOIN Workouts USING (Activity_ID) 
					GROUP BY Activity_ID ORDER BY num DESC LIMIT 0,1');
		$row = mysql_fetch_array($result);
		$activity = new Activity();
		$activity->load($row['Activity_ID']);
		return $activity;
	}

	private function most_kcal($db){
		$result = $db->query('SELECT User_ID, SUM(Kcal) AS Kcal FROM Workouts GROUP BY User_ID 
					ORDER BY Kcal DESC LIMIT 0,1');
		$row = mysql_fetch_array($result);
		$user = new User();
		$user->load($row['User_ID']);
		return $user;
	}
}

class xmlStats implements View {

	private $type = 'stats+xml';
	private $writer;

	public function xmlStats(){		
	}

	public function parse($data, $stat){
		// no parse as stats are not submittable
	}

	public function generateDocument($stats){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($stats, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($stats, &$writer){
		$writer->startElement('statistics');
		
		$writer->startElement('user_count');
		$writer->text($stats->user_count);
		$writer->endElement();

		$writer->startElement('longest_workout');
		$view = new xmlWorkout();		
		$view->addElements($stats->longest_workout, $writer);
		$writer->endElement();

		$writer->startElement('biggest_workout');
		$view = new xmlWorkout();		
		$view->addElements($stats->biggest_workout, $writer);
		$writer->endElement();

		$writer->startElement('popular_activity');
		$view = new xmlActivity();		
		$view->addElements($stats->popular_activity, $writer);
		$writer->endElement();

		$writer->startElement('most_kcal');
		$view = new xmlUser();
		$view->addElements($stats->most_kcal, $writer);
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

class jsonStats implements View {

	private $type = 'stats+json';

	public function jsonStats(){		
	}

	public function parse($data, $stat){
		// no parse as stats are not submittable
	}

	public function generateDocument($stats){
		$this->data = array();
		$this->addElements($stats, $this->data);
	}

	public function addElements($stats, &$data){
		$tmp = new jsonUser();
		$tmp->addElements($stats->most_kcal, $user);
		$tmp = new jsonWorkout();
		$tmp->addElements($stats->biggest_workout, $biggest);
		$tmp = new jsonWorkout();
		$tmp->addElements($stats->longest_workout, $longest);
		$tmp = new jsonActivity();
		$tmp->addElements($stats->popular_activity, $popular);
		$data = array('statistics' => 
				array(
					'user_count' => $stats->user_count,
					'longest_workout' => $longest,
					'biggest_workout' => $biggest,
					'popular_activity' => $popular,
					'most_kcal' => $user
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

class yamlStats implements View {

	private $type = 'stats+yaml';

	public function yamlStats(){		
	}

	public function parse($data, $stat){
		// no parse as stats are not submittable
	}

	public function generateDocument($stats){
		$this->data = array();
		$this->addElements($stats, $this->data);
	}

	public function addElements($stats, &$data){
		$tmp = new yamlUser();
		$tmp->addElements($stats->most_kcal, $user);
		$tmp = new yamlWorkout();
		$tmp->addElements($stats->biggest_workout, $biggest);
		$tmp = new yamlWorkout();
		$tmp->addElements($stats->longest_workout, $longest);
		$tmp = new yamlActivity();
		$tmp->addElements($stats->popular_activity, $popular);
		$data = array('statistics' => 
				array(
					'user_count' => $stats->user_count,
					'longest_workout' => $longest,
					'biggest_workout' => $biggest,
					'popular_activity' => $popular,
					'most_kcal' => $user
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
