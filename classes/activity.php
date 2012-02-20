<?php

require_once LIB . 'CRUD.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Represents an Activity a workout may include.
 * 	@namespace Exerciser
 */
class Activity extends CRUD {

	protected $type = 'activity';

	protected $activity_id = 0;
	protected $activity_name = '';
	protected $description = '';
	protected $kcal_hour = 0;

	/**
	 * @access public
	 */
	public function __construct($db){		
		parent::__construct($db);
	}

	/**
	 * Loads the  details of the activity specified by ID number
	 * @access public
	 * @param int the activity id number
	 */
	public function load($activityID){
		$id = $this->db->clean($activityID);
		$result = $this->db->query("SELECT 	Activity_ID,
							Activity_Name,
							Description,
							Kcal_Hour
							FROM Activities WHERE Activity_ID = '$id'"
					);

		if( ! $result || count($result) == 0 )
			throw new LengthException("No matching entry in database");

		$row = $result[0];
		$this->activity_id = $row['Activity_ID'];
		$this->activity_name = $row['Activity_Name'];
		$this->description = $row['Description'];
		$this->kcal_hour = $row['Kcal_Hour'];
	}

	/**
	 * Adds elements to the specified view
	 * @param View type of document requested
	 */
	public function addProperties($view){
		$view->startElement('activity');

		$view->startElement('activity_id');
		$view->text($this->activity_id);
		$view->endElement();

		$view->startElement('activity_name');
		$view->text($this->activity_name);
		$view->endElement();

		$view->startElement('description');
		$view->text($this->description);
		$view->endElement();

		$view->startElement('kcal_hour');
		$view->text($this->kcal_hour);
		$view->endElement();
		
		$view->endElement();
	}
}

?>
