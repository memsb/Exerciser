<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';
require_once dirname(__FILE__) . '/../lib/utils.php';

class User extends CRUD {

	//values
	protected $user_id;
	protected $password;
	protected $username;
	protected $age;
	protected $weight;
	protected $gender;

	protected $required = array('username', 'age', 'weight', 'gender');

	public function User($uri){
		$this->uri = $uri;
	}

	public function load($userID){
		// load in stats from database
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT 	User_ID,
						Username,
						Password, 
						Age, 
						Weight, 
						Gender
						FROM Users WHERE User_ID = '" . mysql_real_escape_string($userID) . "'"
					);
		if( mysql_num_rows($result) == 0 ){
			throw new Exception("No matching entry in database");
		}
		$row = mysql_fetch_array($result);
		$this->user_id = $row['User_ID'];
		$this->password = $row['Password'];
		$this->username = $row['Username'];
		$this->age = $row['Age'];
		$this->weight = $row['Weight'];
		$this->gender = $row['Gender'];
	}

	public function create(){
		//$this->validate();
		$this->password = generateAPIKey();
		$db = new Database();
		$db->connect();
		$result = $db->query("INSERT INTO Users (	Username,
								Password, 
								Age, 
								Weight, 
								Gender
								) VALUES ('" . 
								mysql_real_escape_string($this->username) . "', '" . 
								$this->password . "', '" .
								mysql_real_escape_string($this->age) . "', '" . 
								mysql_real_escape_string($this->weight) . "', '" . 
								mysql_real_escape_string($this->gender) . "')"
					);
		$this->user_id = mysql_insert_id();
		$this->uri .= '/' . $this->user_id;
	}

	public function update(){
		$this->validate();
		$db = new Database();
		$db->connect();
		$result = $db->query("UPDATE Users SET 	
							Username = 	'" . mysql_real_escape_string($this->username) . "',
							Age = 		'" . mysql_real_escape_string($this->age) . "',
							Weight = 	'" . mysql_real_escape_string($this->weight) . "',
							Gender = 	'" . mysql_real_escape_string($this->gender) . "'
					WHERE User_ID = '" . mysql_real_escape_string($this->user_id) . "'"
					);
	}

	public function delete(){
		// add user to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Users WHERE User_ID = '" . mysql_real_escape_string($this->user_id) . "'");
	}

	public function validate(){
		foreach( $this->required as $field )
			if( ! isset($this->$field) )
				throw new UnexpectedValueException("Data for field: $field required but not present");
	}

	public function location(){
		return $this->uri;
	}
}


class xmlUser implements View {

	private $type = 'application/xml+user';
	private $writer;
	private $data;

	public function xmlUser(){
	}

	public function parse($data, $user){
			
		//check mime type
		libxml_use_internal_errors(true);
		$this->data = simplexml_load_string($data);
		if (!$this->data)
			throw new Exception("Unable to parse XML");
		
		//parse xml into values
		foreach( $user->required as $field )
			$user->$field = (string)$this->data->$field;
		
		$user->validate();
	}

	public function generateNewUserDocument($user){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->writer->startElement('new_user');
		$this->writer->writeAttribute('uri', $user->location());
		
		$this->writer->startElement('user_id');
		$this->writer->text($user->user_id);
		$this->writer->endElement();

		$this->writer->startElement('api_key');
		$this->writer->text($user->password);
		$this->writer->endElement();

		$this->writer->endElement();
		$this->writer->endDocument();
	}

	public function generateDocument($user){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($user, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($user, &$writer){
		$writer->startElement('user');
		$writer->writeAttribute('uri', $user->location());
		
		$writer->startElement('user_id');
		$writer->text($user->user_id);
		$writer->endElement();

		$writer->startElement('username');
		$writer->text($user->username);
		$writer->endElement();
		
		$writer->startElement('age');
		$writer->text($user->age);
		$writer->endElement();

		$writer->startElement('weight');
		$writer->text($user->weight);
		$writer->endElement();

		$writer->startElement('gender');
		$writer->text($user->gender);
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

class jsonUser implements View {

	private $type = 'application/json+user';

	public function jsonUser(){		
	}

	public function parse($data, $user){
		$this->data = json_decode($data, true);
		foreach( $user->required as $field )
			$user->$field = $this->data['user'][$field];
		$user->validate();
	}

	public function generateNewUserDocument($user){
		$this->data = array('new_user' => 
					array(
						'user_id' => $user->user_id,
						'api_key' => $user->password
					),
					'uri' => $user->location() . '.json'
				);
	}

	public function generateDocument($user){
		$this->data = array();
		$this->addElements($user, $this->data);
	}

	public function addElements($user, &$data){
		$data = array('user' => 
				array(
					'user_id' => $user->user_id,
					'username' => $user->username,
					'age' => $user->age,
					'weight' => $user->weight,
					'gender' => $user->gender
				),
				'uri' => $user->location() . '.json'
			);
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return json_encode($this->data);
	}
}

class yamlUser implements View {

	private $type = 'text/x-yaml+user';

	public function yamlUser(){		
	}

	public function parse($data, $user){
		$this->data = yaml_parse($data);
		foreach( $this->required as $field )
			$user->$field = $this->data['user'][$field];
		$user->validate();
	}

	public function generateNewUserDocument($user){
		$this->data = array('new_user' => 
					array(
						'user_id' => $user->user_id,
						'api_key' => $user->password
					),
					'uri' => $user->location() . '.json'
				);
	}

	public function generateDocument($user){
		$this->data = array();
		$this->addElements($user, $this->data);
	}

	public function addElements($user, &$data){
		$data = array('user' => 
				array(
					'user_id' => $user->user_id,
					'username' => $user->username,
					'age' => $user->age,
					'weight' => $user->weight,
					'gender' => $user->gender
				),
				'uri' => $user->location() . '.yaml'
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
