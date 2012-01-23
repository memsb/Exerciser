<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';

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
		if( mysql_num_rows($result) == 0 ){
			throw new Exception("No matching entries in database");
		}
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

	public function addElements($activities, &$writer){
		$writer->startElement('activities');
		$view = new xmlActivity();
		foreach($activities->activities as $activity){			
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

class jsonActivities implements View {

	private $type = 'activities+xml';
	private $data;

	public function jsonActivities(){
	}

	public function parse($data, $activities){
	}

	public function generateDocument($activities){
		$data = array();
		$this->addElements($activities, $data);
		$this->data['activities'] = $data;
	}

	public function addElements($activities, &$data){
		$view = new jsonActivity();
		foreach($activities->activities as $activity){			
			$view->addElements($activity, $entry);
			$data[] = $entry;
		}
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return json_encode($this->data);
	}
}

class yamlActivities implements View {

	private $type = 'activities+yaml';
	private $data;

	public function yamlActivities(){
	}

	public function parse($data, $activities){
	}

	public function generateDocument($activities){
		$data = array();
		$this->addElements($activities, $data);
		$this->data['activities'] = $data;
	}

	public function addElements($activities, &$data){		
		$view = new yamlActivity();
		foreach($activities->activities as $activity){
			$view->addElements($activity, $entry);
			$data[] = $entry;
		}
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return yaml_emit($this->data);
	}
}

?>
