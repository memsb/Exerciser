<?php

require_once('DBA.php');
require_once('interfaces.php');

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

	public function addElements($user, $writer){
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

?>
