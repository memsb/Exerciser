<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';

/**
*	@author Martin Buckley - MBuckley@gmail.com
*	Represents an Activity a workout may include.
*/
class Activity extends CRUD {

	protected $activity_id = 0;
	protected $activity_name = '';
	protected $description = '';
	protected $kcal_hour = 0;

	/**
	* @access public
	*/
	public function __construct(){
		$this->db = new Database();
	}

	/**
	* @access public
	* @param Database instance
	*/
	public function setDatabase($db){
		$this->db = $db;
	}

	/**
	* Loads the  details of the activity specified by ID number
	* @access public
	* @param int the activity id number
	*/
	public function load($activityID){
		if( ! isset($this->db) )
			throw new BadMethodCallException('Database not specified.');

		$this->db->connect();
		$id = $this->db->clean($activityID);
		$result = $this->db->query("SELECT 	Activity_ID,
							Activity_name,
							Description,
							kcal_hour
							FROM Activities WHERE Activity_ID = '$id'"
					);
		
		if( count($result) == 0 )
			throw new LengthException("No matching entry in database");
		
		$this->activity_id = $result['Activity_ID'];
		$this->activity_name = $result['Activity_name'];
		$this->description = $result['Description'];
		$this->kcal_hour = $result['kcal_hour'];
	}
}

/**
*	@author Martin Buckley - MBuckley@gmail.com
*	An XML View of the Activity class.
*/
class xmlActivity extends View {

	protected $type = 'application/xml+activity';

	/**
	* Builds the XML document and stores internally
	* @param Activity model to create a view of
	* @access public
	*/
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

	/**
	* Adds the elements to the writer object passed
	* @param Activity model to create a view of
	* @param XMLWriter to add the elements to
	* @access public
	*/
	public function addElements($activity, &$writer){
		$writer->startElement('activity');
		$writer->writeAttribute('uri', $activity->location());

		$writer->startElement('activity_id');
		$writer->text($activity->activity_id);
		$writer->endElement();

		$writer->startElement('activity_name');
		$writer->text($activity->activity_name);
		$writer->endElement();

		$writer->startElement('description');
		$writer->text($activity->description);
		$writer->endElement();

		$writer->startElement('kcal_hour');
		$writer->text($activity->kcal_hour);
		$writer->endElement();
		
		$writer->endElement();
	}

	/**
	* Outputs the document as a string
	* @access public
	* @return String containing to document
	*/
	public function toString(){
		if($this->writer != null){		
			return $this->writer->outputMemory();
		}else{
			return '';
		}
	}
}



/**
*	@author Martin Buckley - MBuckley@gmail.com
*	An JSON View of the Activity class.
*/
class jsonActivity extends View {

	protected $type = 'application/json+activity';

	/**
	* Builds the JSON document and stores internally
	* @param Activity model to create a view of
	* @access public
	*/
	public function generateDocument($activity){
		$this->data = array();
		$this->addElements($activity, $this->data);
	}

	/**
	* Adds the elements to the writer object passed
	* @param Activity model to create a view of
	* @param Array to add the elements to
	*/
	public function addElements($activity, &$data){
		$data = array('activity' => 
				array(
					'activity_id' => $activity->activity_id,
					'activity_name' => $activity->activity_name,
					'description' => $activity->description,
					'kcal_hour' => $activity->kcal_hour
				),
				'uri' => $activity->location() . '.json'
			);
	}

	/**
	* Outputs the document as a string
	* @access public
	* @return String containing to document
	*/
	public function toString(){
		if( $this->data )
			return json_encode($this->data);
		else
			return '';
	}
}



/**
*	@author Martin Buckley - MBuckley@gmail.com
*	An YAML View of the Activity class.
*/
class yamlActivity extends View {

	protected $type = 'text/x-yaml+activity';

	/**
	* Builds the YAML document and stores internally
	* @param Activity model to create a view of
	* @access public
	*/
	public function generateDocument($activity){
		$this->addElements($activity, $this->data);
	}

	/**
	* Adds the elements to the writer object passed
	* @param Activity model to create a view of
	* @param Array to add the elements to
	* @access public
	*/
	public function addElements($activity, &$data){
		$data = array('activity' => 
				array(
					'activity_id' => $activity->activity_id,
					'activity_name' => $activity->activity_name,
					'description' => $activity->description,
					'kcal_hour' => $activity->kcal_hour
				),
				'uri' => $activity->location() . '.yaml'
			);
	}
 
	/**
	* Outputs the document as a string
	* @access public
	* @return String containing to document
	*/
	public function toString(){		
		if( $this->data )
			return yaml_emit($this->data);
		else
			return '';
	}
}

?>
