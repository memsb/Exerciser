<?php

require_once('DBA.php');
require_once('interfaces.php');
require_once('user.php');

class Stats extends CRUD {

	//stat values
	protected $user_count = 0;
	protected $longest_workout = "";
	protected $biggest_workout = "";
	protected $most_kcal = '';

	public function Stats(){
	}

	public function load(){
		// load in stats from database
		$db = new Database();
		$db->connect();

		$this->user_count = $this->countUsers($db);
		$this->longest_workout = $this->longestWorkout($db);
		$this->biggest_workout = $this->biggestWorkout($db);
		$this->most_kcal = $this->most_kcal($db);
	}

	private function countUsers($db){
		$result = $db->query('SELECT COUNT(*) AS user_count FROM Users');
		$row = mysql_fetch_array($result);
		return $row['user_count'];
	}

	private function longestWorkout($db){
		$result = $db->query('SELECT Workout_ID, Start_Time, Duration, Kcal FROM Workouts
					ORDER BY Duration DESC LIMIT 0,1');
		$row = mysql_fetch_array($result);
		return $row['Workout_ID'];
	}

	private function biggestWorkout($db){
		$result = $db->query('SELECT Workout_ID, Start_Time, Duration, Kcal FROM Workouts
					ORDER BY Kcal DESC LIMIT 0,1');
		$row = mysql_fetch_array($result);
		return $row['Workout_ID'];
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

	public function addElements($stats, $writer){
		$writer->startElement('statistics');
		
		$writer->startElement('user_count');
		$writer->text($stats->user_count);
		$writer->endElement();

		$writer->startElement('longest_workout');
		$writer->text($stats->longest_workout);
		$writer->endElement();

		$writer->startElement('biggest_workout');
		$writer->text($stats->biggest_workout);
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

?>
