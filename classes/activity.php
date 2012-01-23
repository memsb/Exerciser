<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';

class Activity extends CRUD {

	protected $activity_id;
	protected $activity_name;
	protected $kcal_hour;

	public function Activity(){
	}

	public function load($activityID){
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT 	Activity_ID,
						Activity_name,
						kcal_hour
						FROM Activities WHERE Activity_ID = '" . $activityID . "'"
					);		
		if( mysql_num_rows($result) == 0 ){
			throw new Exception("No matching entry in database");
		}
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
	private $data;
	private $writer;

	public function xmlActivity(){
	}

	public function parse($data, $activity){		
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

	public function addElements($activity, &$writer){
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

class jsonActivity implements View {

	private $type = 'activity+json';
	private $data;

	public function jsonActivity(){
	}

	public function parse($data, $activity){	
		$this->data = json_decode($data, true);
		$activity->activity_id = $this->data['activity']['activity_id'];
		$activity->activity_hour = $this->data['activity']['activity_hour'];
		$activity->kcal_hour = $this->data['activity']['kal_hour'];
	}

	public function generateDocument($activity){
		$this->data = array();
		$this->addElements($activity, $this->data);
	}

	public function addElements($activity, &$data){
		$data = array('activity' => 
				array(
					'acivity_id' => $activity->activity_id,
					'activity_name' => $activity->activity_name,
					'kcal_hour' => $activity->kcal_hour
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

class yamlActivity implements View {

	private $type = 'activity+yaml';
	private $data;

	public function yamlActivity(){
	}

	public function parse($data, $activity){	
		$this->data = yaml_parse($data);
		$activity->activity_id = $this->data['activity']['activity_id'];
		$activity->activity_hour = $this->data['activity']['activity_hour'];
		$activity->kcal_hour = $this->data['activity']['kal_hour'];
	}

	public function generateDocument($activity){
		$this->addElements($activity, $this->data);
	}

	public function addElements($activity, &$data){
		$data = array('activity' => 
				array(
					'acivity_id' => $activity->activity_id,
					'activity_name' => $activity->activity_name,
					'kcal_hour' => $activity->kcal_hour
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
