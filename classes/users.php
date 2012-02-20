<?php

require_once LIB . 'CRUD.php';
require_once CLASSES . '/user.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Users stores a list of User instances
 * 	@namespace Exerciser
 */
class Users extends CRUD {

	protected $type = 'users';
	protected $users = array();

	/**
	 * Constructor calls constructor in CRUD
	 * @param Database instance to be used
	 */
	public function __construct($db){
		parent::__construct($db);
	}

	/**
	 * Gives a count of User loaded
	 * @param Int the count
	 */
	public function size(){
		return count($this->users);
	}

	/**
	 * Adds a User to the user list
	 * @param User the user
	 */
	public function addUser(User $user){
		$this->users[] = $user;
	}

	/**
	 * Loads all User from database
	 */
	public function load(){
		$result = $this->db->query("SELECT User_ID FROM Users");		
		foreach($result as $row){
			$id = $row['User_ID'];
			$user = new User($this->db);
			$user->setLocation($this->uri . '/' . $id);
			$user->load($id);
			$this->users[] = $user;
		}
	}

	/**
	 * Generates a document representation of the model in the requested view
	 * @param View type requested
	 * @return String containing the document
	 */
	public function generateDocument($view){
		$view->startDocument();
		$view->startElement('users');
		$i = 0;
		foreach($this->users as $user){
			$view->startElement($i++);		
			$user->addProperties($view);
			$view->endElement();
		}

		$view->endElement();
		return $view->toString();
	}
}

?>
