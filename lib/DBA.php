<?php

require_once '/var/www/exerciser/config.php';

/**
*	@author Martin Buckley - MBuckley@gmail.com
*	DataBase Abstraction layer controlls access to the database.
*/
class Database{

	/**
	* @var The database connection
	*/
	private $connection = null;
	private $results = array();

	/**
	* Constructor sets the database access parameters
	* @param String the host name
	* @param String the database
	* @param String the username
	* @param String the password
	*/
	public function __construct($host = HOST, $database = DATABASE, $username = USERNAME, $password = PASSWORD){
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	* Sanitises the variable to prevent SQL injection
	* @param String the value
	* @return String the sanitised value
	*/
	public function clean($value){
		return mysql_real_escape_string($value);
	}

	/**
	* Checks if a valid database connection exists
	* @return Boolean True if connected
	*/
	public function connected(){
		return $this->connection != null;
	}

	/**
	* Connects to the database
	*/
	public function connect(){
		$this->connection = mysql_connect($this->host, $this->username, $this->password);

		if (!$this->connection)
			throw new Exception("Could not connect to SQL database");

		$db_selected = mysql_select_db($this->database, $this->connection);

		if (!$db_selected)
			throw new Exception("Could not select database");
	}

	/**
	* Performs the specified query. Results are returned as an array
	* @param String the SQL query
	* @return array of results
	*/
	public function query($query){
		$result = mysql_query($query, $this->connection);
		$this->results = array();
		while($row = mysql_fetch_array($result)){
			$this->results[] = $row;
		}
		return $this->results;
	}
}
?>
