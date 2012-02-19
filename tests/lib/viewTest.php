<?php

require_once('/var/www/exerciser/lib/view.php');

class TestOfNodeClass extends UnitTestCase {

	private $node;
	private $name = 'test_name';
	private $value = 'test_value';

	function setup() {
		$this->node = new Node();
		$this->node->value = $this->value;
		
	}

	function testName() {
		$this->assertEqual($this->node->name, '');
		$this->node->name = $this->name;
		$this->assertEqual($this->node->name, $this->name);
	}

	function testValue() {
		$this->assertEqual($this->node->value, $this->value);
		$this->node->value = '';
		$this->assertEqual($this->node->value, '');
	}

	function testChildren() {
		$this->assertEqual(count($this->node->children), 0);
		$child1 = new Node();
		$this->node->addChild($child1);
		$this->assertEqual(count($this->node->children), 1);
		$this->assertEqual($this->node->children[0], $child1);
		$child2 = new Node();
		$this->node->addChild($child2);
		$this->assertEqual(count($this->node->children), 2);
		$this->assertEqual($this->node->children[1], $child2);
	}
}


class TestOfTreeClass extends UnitTestCase {

	private $tree;
	private $name = 'test_name';
	private $text = 'test_text';

	function setup() {
		$this->tree = new Tree();
	}

	function testType(){
		$this->assertEqual($this->tree->type(), '');
	}

	function testAddChildren(){
		$this->assertNull($this->tree->head);
		$this->assertTrue($this->tree->isEmpty());

		$this->tree->startElement('a');
		$this->assertFalse($this->tree->isEmpty());
		$this->assertNotNull($this->tree->head);
		$this->assertEqual(count($this->tree->head->children), 0);		

		$this->tree->startElement('b');
		$this->tree->text('b text');
		$this->tree->endElement();
		
		$this->assertEqual(count($this->tree->head->children), 1);
		$child = $this->tree->head->children[0];
		$this->assertEqual($child->name, 'b');
		$this->assertEqual($child->value, 'b text');

		$this->tree->startElement('c');
		$this->tree->text('c text');
		$this->tree->endElement();

		$this->assertEqual(count($this->tree->head->children), 2);
		$child = $this->tree->head->children[1];
		$this->assertEqual($child->name, 'c');
		$this->assertEqual($child->value, 'c text');

		$this->tree->endElement();

	}

	function testAddGrandChildren(){

		$this->tree->startElement('a');
		$this->tree->text('a text');

		$this->tree->startElement('b');
		$this->tree->text('b text');

		$child = $this->tree->head->children[0];
		$this->assertEqual(count($child->children), 0);

		$this->tree->startElement('b1');
		$this->tree->text('b1 text');
		$this->tree->endElement();

		$this->assertEqual(count($child->children), 1);
		$grandchild = $child->children[0];			
		$this->assertEqual(count($grandchild->children), 0);
		$this->assertEqual($grandchild->name, 'b1');
		$this->assertEqual($grandchild->value, 'b1 text');

		$this->tree->endElement();
	}

	function testIterationOnEmptyTree(){
		$empty = $this->tree->iterate($this->tree->head);
		$this->assertNull($empty);
	}

	function testIteration(){
		$data = array('root' => array('a' => array('a1' => 'a1 text'), 'b' => 'b text', 'c' => 'c text', 'd' => 'd text') );
		$this->build($data);
		
		$list = $this->tree->iterate($this->tree->head);
		$this->assertEqual($list, $data);
		
	}

	function testIsHead(){
		$this->assertNull($this->tree->head);
		$this->assertTrue($this->tree->isHead($this->tree->head));

		$this->tree->startElement($this->name);
		$this->tree->text($this->text);
		$this->tree->endElement();

		$this->assertNotNull($this->tree->head);
		$this->assertTrue($this->tree->isHead($this->tree->head));
	}

	protected function build($data){
		foreach($data as $key => $value){
			$this->tree->startElement($key);
			if( is_array($value) )
				$this->build($value);
			else
				$this->tree->text($value);
			$this->tree->endElement();
		}
	}
}

class TestOfXMLTreeClass extends UnitTestCase {

	private $tree;

	function setup() {
		$this->tree = new XML();
	}

	function testType(){
		$this->assertEqual($this->tree->type(), 'application/xml+');
	}

	function testParseAndOutput(){
		$xml_str = 	'<?xml version="1.0" encoding="UTF-8"?>
				 <root>
				 <a>
				  <a1>a1 text</a1>
				 </a>
				 <b>b text</b>
				 <c>c text</c>
				 <d>d text</d>
				</root>';
		
		libxml_use_internal_errors(true);
		$input = simplexml_load_string($xml_str);

		$this->tree->parse($xml_str);
		$output = simplexml_load_string($this->tree->toString());
		
		$this->assertEqual(preg_replace("'\s+'", '', $input), preg_replace("'\s+'", '', $output));
	}
}

class TestOfJSONTreeClass extends UnitTestCase {

	private $tree;

	function setup() {
		$this->tree = new JSON();
	}

	function testType(){
		$this->assertEqual($this->tree->type(), 'application/json+');
	}

	function testParseAndOutput(){
		$input = 	'{"root":{"a": {"a1": "a1 text"},"b": "b text","c": "c text","d": "d text"}}';

		$this->tree->parse($input);
		$output = $this->tree->toString();

		$this->assertEqual(preg_replace("'\s+'", '', $input), preg_replace("'\s+'", '', $output));
	}
}

class TestOfYAMLTreeClass extends UnitTestCase {

	private $tree;

	function setup() {
		$this->tree = new YAML();
	}

	function testType(){
		$this->assertEqual($this->tree->type(), 'text/x-yaml+');
	}

	function testParseAndOutput(){
		$input = <<<YAML
---
root:
 a: 
    a1: a1 text
 b:  b text
 c:  c text
 d:  d text
...
YAML;

		$this->tree->parse($input);
		$output = $this->tree->toString();

		$this->assertEqual(preg_replace("'\s+'", '', $input), preg_replace("'\s+'", '', $output));
	}
}








?>
