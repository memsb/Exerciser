<?php

require_once('DBA.php');
require_once('interfaces.php');

class Activity extends CRUD {

	//values
	protected $activity_id;
	protected $activity_name;
	protected $kcal_hour;

	public function Activity(){
	}

	public function load($activityID){
		// load in stats from database
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT 	Activity_ID,
						Activity_name,
						kcal_hour
						FROM Activities WHERE Activity_ID = '" . $activityID . "'"
					);
		$row = mysql_fetch_array($result);
		$this->activity_id = $row['Activity_ID'];
		$this->activity_name = $row['Activity_name'];
		$this->kcal_hour = $row['kcal_hour'];
	}

	public function location(){
		return $this->activity_id;
	}
}


class xmlActivity implements View {

	private $type = 'activity+xml';
	private $writer;
	private $data;

	public function xmlActivity(){
	}

	public function parse($data, $user){		
		//check mime type
		$this->data = new SimpleXMLElement($data);
		//parse xml into values
		$activity->activity_id = $this->data->activity_id;
		$activity->activity_hour = $this->data->activity_hour;
		$activity->kcal_hour = $this->data->kal_hour;
	}

	public function generateDocument($activity){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($activity, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($activity, $writer){
		$writer->startElement('activity');
		
		$writer->startElement('acivity_id');
		$writer->text($activity->activity_id);
		$writer->endElement();

		$writer->startElement('activity_name');
		$writer->text($activity->activity_name);
		$writer->endElement();

		$writer->startElement('kcal_hour');
		$writer->text($activity->kcal_hour);
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
