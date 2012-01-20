<?php

require_once('DBA.php');

class User{

	//stat values
	public $username;
	public $password;
	public $age;
	public $weight;
	public $gender;

	public function User(){
	}

	//getters & setters

	public function create(){
		echo $this->username;
		// load in stats from database
		$db = new Database('localhost', 'exercise', 'exerciser', 'exerciser');
		$db->connect();
		$result = $db->query("INSERT INTO Users (Username, Password, Age, Weight, Gender) 
						VALUES ('" . $this->username . "', '" . $this->password . "', '" . $this->age . "', '" . $this->weight . "', '" . $this->gender . "')");
		$db->close();
	}

	public function location(){
		return '/' . $this->username;
	}
}

class xmlUser {

	private $type = 'user+xml';

	public function xmlUser(){
	}

	public function parse($data, $user){

		//parse xml into values
		$user->username = 'test';
		$user->password = '456';
		$user->age = 19;
		$user->weight = 99;
		$user->gender = 'Female';

	}

	public function generate($user){
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
	}
}

?>
