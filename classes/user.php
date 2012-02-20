<?php

require_once LIB . 'CRUD.php';
require_once LIB . 'utils.php';

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	User holds details of a site user, can be used as a part of autherisation
 * 	@namespace Exerciser
 */
class User extends CRUD {

	protected $type = 'user';
	public $user_id = 0;
	public $api_key = 0;
	protected $username = '';
	protected $age = 0;
	protected $weight = 0;
	protected $gender = 'Male';

	protected $required = array('username', 'age', 'weight', 'gender');

	/**
	 * Constructor calls constructor in CRUD
	 * @param Database instance to be used
	 */
	public function __construct($db){
		parent::__construct($db);
	}

	/**
	 * Loads details for the specified User
	 * @param Int the User ID
	 */
	public function load($userID){
		$id = $this->db->clean($userID);
		$result = $this->db->query("SELECT 	User_ID,
							Username,
							API_Key, 
							Age, 
							Weight, 
							Gender
							FROM Users WHERE User_ID = '$id'"
						);
		if(  ! $result || count($result) == 0 )
			throw new LengthException("User not found.");
		
		$row = $result[0];
		$this->user_id = $row['User_ID'];
		$this->api_key = $row['API_Key'];
		$this->username = $row['Username'];
		$this->age = $row['Age'];
		$this->weight = $row['Weight'];
		$this->gender = $row['Gender'];
	}

	/**
	 * Creates a new User given the current details
	 * @return Boolean True on success 
	 */
	public function create(){
		$this->validate();
		$this->api_key = generateAPIKey();
		$username = $this->db->clean($this->username);
		$api_key = $this->api_key;
		$age = $this->db->clean($this->age);
		$weight = $this->db->clean($this->weight);
		$gender = $this->db->clean($this->gender);
		$result = $this->db->query("INSERT INTO Users (	Username,
								API_Key, 
								Age, 
								Weight, 
								Gender
								) VALUES ('$username', '$api_key', '$age', '$weight', '$gender')"
					);
		if ( ! $result )
			throw new Exception("User not created.");

		$this->user_id = $this->db->insert_id();
		$this->uri .= '/' . $this->user_id;
		return True;
	}

	/**
	 * Updates the current users details
	 * @return Boolean True on success
	 */
	public function update(){
		$this->validate();
		$user_id = $this->db->clean($this->user_id);
		$username = $this->db->clean($this->username);
		$age = $this->db->clean($this->age);
		$weight = $this->db->clean($this->weight);
		$gender = $this->db->clean($this->gender);
		$result = $this->db->query("UPDATE Users SET 	
								Username = 	'$username',
								Age = 		'$age',
								Weight = 	'$weight',
								Gender = 	'$gender'
						WHERE User_ID = '$user_id'"
						);
		if ( ! $result )
			throw new Exception("User not updated.");
		return True;
	}

	/**
	 * Deletes User and users Workout from system
	 * @return Boolean True on success
	 */
	public function delete(){
		$id = $this->db->clean($this->user_id);
		$result = $this->db->query("DELETE FROM Users WHERE User_ID = '$id'");
		if ( ! $result )
			throw new Exception("User not deleted.");
		$this->db->query("DELETE FROM Workouts WHERE User_ID = '$id'");
		return True;
	}

	/**
	 * Iterates through the required values to check they are specified
	 */
	public function validate(){
		foreach( $this->required as $field )
			if( ! isset($this->$field) )
				throw new UnexpectedValueException("Data for field: $field required but not present");
	}

	/**
	 * Utilises the View specified to read data from the document
	 * @param String containing the document 
	 * @param View specifying the document type
	 */
	public function parse($data_str, $view){
		if( ! $view )
			throw new BadMethodCallException('Function not implemented.');

		try{
			$view->parse($data_str);
			$data = $view->getArray();
			$user = $data['user'];

			foreach( $this->required as $field )
				$this->$field = $user[$field];
		}catch(Exception $e){
			throw new UnexpectedValueException($e->getMessage());
		}
	}

	/**
	 * Adds elements to the specified View
	 * @param View specifying the document type
	 */
	public function addProperties($view){
		$view->startElement('user');
		
		$view->startElement('user_id');
		$view->text($this->user_id);
		$view->endElement();

		$view->startElement('username');
		$view->text($this->username);
		$view->endElement();
		
		$view->startElement('age');
		$view->text($this->age);
		$view->endElement();

		$view->startElement('weight');
		$view->text($this->weight);
		$view->endElement();

		$view->startElement('gender');
		$view->text($this->gender);
		$view->endElement();

		$view->endElement();
	}

	/**
	 * Returns a String containing the document of the type specified by the view
	 * @param View determining document type
	 * @return String containing the document 
	 */
	public function generateNewUserDocument($view){		
		$view->startDocument();
		$view->startElement('new_user');
		
		$view->startElement('user_id');
		$view->text($this->user_id);
		$view->endElement();

		$view->startElement('api_key');
		$view->text($this->api_key);
		$view->endElement();

		$view->endElement();
		return $view->toString();
	}
}

?>
