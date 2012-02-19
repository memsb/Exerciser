<?php

require_once('/var/www/exerciser/lib/CRUD.php');
require_once('/var/www/exerciser/lib/DBA.php');
require_once('/var/www/exerciser/lib/view.php');

class TestOfCRUDClass extends UnitTestCase {

	private $uri = 'uri';
	private $db;
	private $crud;

	function setup() {
		Mock::generate('Database');
		$this->db = &new MockDatabase();
		$this->db->setReturnValue('connect', '');
		$this->crud = new CRUD($this->db);
	}

	function testNoDatabase(){
		$this->expectException('BadMethodCallException');
		$this->crud = new CRUD(NULL);
	}

	function testSetAndGet(){
		$num = 1;
		$this->crud->test = $num;
		$this->assertEqual($this->crud->test, 1);
	}

	function testType(){		
		$this->assertEqual($this->crud->type(NULL), '');
	}

	function testLocation(){
		$location = 'test';		
		$this->assertEqual($this->crud->location(), '');
		$this->crud->setLocation($location);
		$this->assertEqual($this->crud->location(), $location);
	}

	function testLoad(){
		$this->expectException('BadMethodCallException');
		$this->crud->load(NULL);
	}

	function testCreate(){
		$this->expectException('BadMethodCallException');
		$this->crud->create();
	}

	function testUpdate(){
		$this->expectException('BadMethodCallException');
		$this->crud->update();
	}

	function testDelete(){
		$this->expectException('BadMethodCallException');
		$this->crud->delete();
	}

	function testParse(){
		$this->expectException('BadMethodCallException');
		$this->crud->parse(NULL, NULL);
	}

	function testGenerateDocumentFailure(){
		$this->assertEqual($this->crud->generateDocument(NULL), '');
	}
}

?>
