<?php

require_once('DBA.php');

class Stats{

	//stat values
	public $data = array();

	public function Stats(){
	}

	//getters

	public function load(){
		// load in stats from database
		$db = new Database('localhost', 'exercise', 'exerciser', 'exerciser');
		$db->connect();
		$result = $db->query('SELECT * FROM Users');
		while ($row = mysql_fetch_array($result)) {
			$this->data[] = $row;
		}
		$db->close();
	}
}

class xmlStats {

	private $type = 'stats+xml';
	private $writer;

	public function xmlStats(){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');
	}

	public function generate($stats){
		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		//calls getters on stats

		$this->writer->startElement('users');
		foreach($stats->data as $row){
			$this->writer->startElement('user');
			$this->writer->text($row['Username']);
			$this->writer->endElement();
		}
		$this->writer->endElement();
		$this->writer->endDocument();
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){		
		return $this->writer->outputMemory();
	}
}

?>
