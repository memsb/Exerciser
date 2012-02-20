<?php

require_once LIB . 'CRUD.php';
require_once CLASSES . 'activity.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Contains a list of Activity
 * 	@namespace Exerciser
 */
class Activities extends CRUD {

	protected $type = 'activities';
	protected $activities = array();

	/**
	 * Constructor calls constructor in CRUD
	 * @param Database instance to be used
	 */
	public function __construct($db){		
		parent::__construct($db);
	}

	/**
	 * Loads all activities from database
	 */
	public function load(){		
		$result = $this->db->query("SELECT Activity_ID FROM Activities");		
		foreach($result as $row) {	
			$activity = new Activity($this->db);
			$activity->setLocation($this->uri . '/' . $row['Activity_ID']);
			$activity->load($row['Activity_ID']);
			$this->activities[] = $activity;
		}
	}

	/**
	 * Gives a count of how many Activity have been loaded
	 * @return Int the number of activities loaded
	 */
	public function size(){
		return count($this->activities);
	}

	/**
	 * Gives the list of loaded activities
	 * @return array of Activity
	 */
	public function getActivities(){
		return $this->activities;
	}

	/**
	 * Generates a document representation of the model in the requested view
	 * @param View type requested
	 * @return String containing the document
	 */
	public function generateDocument($view){
		$view->startDocument();
		$view->startElement('activities');
		$i = 0;
		foreach($this->activities as $activity){
			$view->startElement($i++);		
			$activity->addProperties($view);
			$view->endElement();
		}
		$view->endElement();
		return $view->toString();
	}
}

?>
