<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';

class User extends CRUD {

	//values
	protected $user_id;
	protected $username;
	protected $password;
	protected $age;
	protected $weight;
	protected $gender;

	public function User(){
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
						FROM Users WHERE User_ID = '" . $userID . "'"
					);
		if( mysql_num_rows($result) == 0 ){
			throw new Exception("No matching entry in database");
		}
		$row = mysql_fetch_array($result);
		$this->user_id = $row['User_ID'];
		$this->username = $row['Username'];
		$this->age = $row['Age'];
		$this->weight = $row['Weight'];
		$this->gender = $row['Gender'];
	}

	public function create(){
		// add user to database
		$db = new Database();
		$db->connect();
		$result = $db->query("INSERT INTO Users (	Username, 
								Password, 
								Age, 
								Weight, 
								Gender
								) VALUES ('" . 
								$this->username . "', '" . 
								$this->password . "', '" . 
								$this->age . "', '" . 
								$this->weight . "', '" . 
								$this->gender . "')"
					);
		$this->user_id = mysql_insert_id();
	}

	public function update(){
		// add user to database
		$db = new Database();
		$db->connect();
		$result = $db->query("UPDATE Users SET 	
							Username = 	'" . $this->username . "',
							Password =  	'" . $this->password . "',
							Age = 		'" . $this->age . "',
							Weight = 	'" . $this->weight . "',
							Gender = 	'" . $this->gender . "'
					WHERE User_ID = '" . $this->user_id . "'"
					);
	}

	public function delete(){
		// add user to database
		$db = new Database();
		$db->connect();
		$result = $db->query("DELETE FROM Users WHERE User_ID = '" . $this->user_id . "'");
	}

	public function location(){
		return $this->user_id;
	}
}


class xmlUser implements View {

	private $type = 'user+xml';
	private $writer;
	private $data;

	public function xmlUser(){
	}

	public function parse($data, $user){		
		//check mime type
		$this->data = new SimpleXMLElement($data);
		//parse xml into values
		$user->username = $this->data->username;
		$user->password = $this->data->password;
		$user->age = $this->data->age;
		$user->weight = $this->data->weight;
		$user->gender = $this->data->gender;
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
		
		$writer->startElement('user_id');
		$writer->text($user->user_id);
		$writer->endElement();

		$writer->startElement('username');
		$writer->text($user->username);
		$writer->endElement();

		$writer->startElement('password');
		$writer->text('');
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

	private $type = 'user+json';

	public function jsonUser(){		
	}

	public function parse($data, $user){
		$this->data = json_decode($data, true);
		$user_data = $this->data['user'];
		$user->user_id = $user_data['user_id'];
		$user->username = $user_data['username'];
		$user->password = $user_data['password'];
		$user->age = $user_data['age'];
		$user->weight = $user_data['weight'];
		$user->gender = $user_data['gender'];
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
					'password' => $user->password,
					'age' => $user->age,
					'weight' => $user->weight,
					'gender' => $user->gender
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

class yamlUser implements View {

	private $type = 'user+yaml';

	public function yamlUser(){		
	}

	public function parse($data, $user){
		$this->data = yaml_parse($data);
		$user_data = $this->data['user'];
		$user->user_id = $user_data['user_id'];
		$user->username = $user_data['username'];
		$user->password = $user_data['password'];
		$user->age = $user_data['age'];
		$user->weight = $user_data['weight'];
		$user->gender = $user_data['gender'];
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
					'password' => $user->password,
					'age' => $user->age,
					'weight' => $user->weight,
					'gender' => $user->gender
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
