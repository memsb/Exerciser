<?php

require_once('config.php');

class Database{

	private $connection = null;

	public function __construct($host = HOST, $database = DATABASE, $username = USERNAME, $password = PASSWORD){
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
	}

	public function connected(){
		return $this->connection != null;
	}

	public function connect(){
		$this->connection = mysql_connect($this->host, $this->username, $this->password);
		if (!$this->connection){
			die('Could not connect: ' . mysql_error());
		}
		$db_selected = mysql_select_db($this->database, $this->connection);
		if (!$db_selected) {
			die ('Can\'t select database : ' . $this->database . ' ' . mysql_error());
		}
	}

	public function query($query){
		$result = mysql_query($query, $this->connection);
		return $result;
	}
}
?>
