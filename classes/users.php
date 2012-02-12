<?php

require_once dirname(__FILE__) . '/../lib/DBA.php';
require_once dirname(__FILE__) . '/../lib/interfaces.php';
require_once dirname(__FILE__) . '/../lib/utils.php';
require_once dirname(__FILE__) . '/user.php';

class Users extends CRUD {

	protected $users = array();

	public function Users($uri){
		$this->uri = $uri;
	}

	public function load(){
		// load in stats from database
		$db = new Database();
		$db->connect();
		$result = $db->query("SELECT User_ID FROM Users");
		
		while($row = mysql_fetch_array($result)){
			$id = $row['User_ID'];
			$user = new User($this->uri . '/' . $id);
			$user->load($id);
			$this->users[] = $user;
		}
	}

	public function location(){
		return $this->uri;
	}
}


class xmlUsers implements View {

	private $type = 'application/xml+users';
	private $writer;
	private $data;

	public function xmlUser(){
	}

	public function parse($data, $user){		
		//lists of users cannot be passed
	}

	public function generateDocument($users){
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');

		// builds xml document	
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->addElements($users, $this->writer);
		$this->writer->endDocument();
	}

	public function addElements($users, &$writer){
		$writer->startElement('users');

		foreach($users->users as $user){
			$view = new xmlUser();
			$view->addElements($user, $writer);
		}

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

class jsonUsers implements View {

	private $type = 'application/json+users';

	public function jsonUser(){		
	}

	public function parse($data, $users){
		
	}

	public function generateDocument($users){
		$data = array();
		$this->addElements($users, $data);
		$this->data['users'] = $data;
	}

	public function addElements($users, &$data){
		$view = new jsonUser();
		foreach($users->users as $user){			
			$view->addElements($user, $entry);
			$data[] = $entry;
		}
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return json_encode($this->data);
	}
}

class yamlUsers implements View {

	private $type = 'text/x-yaml+users';

	public function yamlUser(){		
	}

	public function parse($data, $users){
		
	}

	public function generateDocument($users){
		$data = array();
		$this->addElements($users, $data);
		$this->data['users'] = $data;
	}

	public function addElements($users, &$data){
		$view = new yamlUser();
		foreach($users->users as $user){			
			$view->addElements($user, $entry);
			$data[] = $entry;
		}
	}

	public function type(){
		return $this->type;
	}
 
	public function toString(){
		return yaml_emit($this->data);
	}
}

?>
