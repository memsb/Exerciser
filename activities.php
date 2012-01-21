<?php

require_once('DBA.php');
require_once('interfaces.php');

require_once('activity.php');

class Activities extends CRUD {

	//values
	protected $activities = array();

	public function Activities(){
	}

	public function load(){
		// load in stats from database
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT Activity_ID FROM Activities");
		while($row = mysql_fetch_array($result)){
			$activity = new Activity();
			$activity->load($row['Activity_ID']);
			$this->activities[] = $activity;
		}
	}
}


class xmlActivities implements View {

	private $type = 'activities+xml';
	private $writer;
	private $data;

	public function xmlActivity(){
	}

	public function parse($data, $activities){
	}

	public function generateDocument($activities){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($activities, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($activities, $writer){
		$writer->startElement('activities');
		
		foreach($activities->activities as $activity){
			$view = new xmlActivity();
			$view->addElements($activity, $writer);
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
