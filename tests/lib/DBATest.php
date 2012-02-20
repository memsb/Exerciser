<?php

require_once(LIB . 'DBA.php');

define('TEST_DATABASE', 'test', true);
define('TEST_USERNAME', 'test', true);
define('TEST_PASSWORD', 'test', true);


/*
 * @namespace Exerciser\Tests\Lib
 */
class TestOfDBAClass extends UnitTestCase {
	
	private $db;

	function setup() {
		$this->db = new Database(HOST, TEST_DATABASE, TEST_USERNAME, TEST_PASSWORD);
		$this->db->connect();
	}

	function testConnectFailure(){
		$this->db = new Database(HOST, '', TEST_USERNAME, TEST_PASSWORD);
		$this->expectException('Exception');
		$this->db->connect();
	}

	function testConnectSuccess(){		
		$this->assertTrue($this->db->connected());
	}

	function testClean(){
		$input = array(1,2,-1,'t','test', '[]', "t", "test", "[]", "\n", "'", "`", "'INSERT");
		$output = array(1,2,-1,'t','test', '[]', "t", "test", "[]", '\n', "\'", "`", "\'INSERT");
		for($i=0; $i<count($input); $i++)
			$this->assertEqual($this->db->clean($input[$i]), $output[$i]);
	}

	function testQuery(){
		$values = array(1,2,3);
		foreach($values as $value)
			$this->db->query("INSERT INTO test (num) VALUES ($value)");

		foreach($values as $value){
			$result = $this->db->query("SELECT num FROM test WHERE num=$value");
			$this->assertTrue(is_array($result));
			$this->assertEqual($value, $result[0]['num']);
		}

		$result = $this->db->query("SELECT num FROM test");
		for($i=0; $i<count($result); $i++){
			$this->assertTrue(is_array($result[$i]));
			$this->assertEqual($values[$i], $result[$i]['num']);
		}
		foreach($values as $value){
			$new_value = $value+1;
			$result = $this->db->query("UPDATE test SET num='$new_value' WHERE num=$value");
			$this->assertTrue($result);
		}
		$result = $this->db->query("DELETE FROM test");
		$this->assertTrue($result);
	}

	function testInsertID(){
		$this->assertEqual($this->db->insert_id(), 0);
		$this->db->query('INSERT INTO test (num) VALUES (1)');
		$this->assertEqual($this->db->insert_id(), 1);
		$this->db->query('INSERT INTO test (num) VALUES (7)');
		$this->assertEqual($this->db->insert_id(), 2);
		$this->db->query('SELECT * FROM test');
		$this->assertEqual($this->db->insert_id(), 2);
	}

	function tearDown(){
		$this->db->query('TRUNCATE test');
	}
}

?>
