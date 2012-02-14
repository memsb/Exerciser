<?php

/**
*
*
*/
class Node {

	public $name;
	public $value;
	public $children = array();

	/**
	*
	*
	*/
	public function __construct($name=''){
		$this->name = $name;
	}

	/**
	*
	*
	*/
	public function text($text){
		$this->value = $text;
	}

	/**
	*
	*
	*/
	public function addChild(Node $child){
		$this->children[] = $child;
	}

	/**
	*
	*
	*/
	public function isLeaf(){
		return count($this->children) == 0;
	}
}

/**
*
*
*/
class Tree {

	public $head;
	private $current;
	private $route = array();
	private $type = '';	

	/**
	*
	*
	*/
	public function __construct(){
		$this->create();
	}
	
	/**
	*
	*
	*/
	public function create(){
		$this->head = new Node('root');
		$this->current = $this->head;
		$this->route = array();		
	}

	/**
	*
	*
	*/
	public function end(){
		$this->current = $this->head;
		$this->route = array();
	}

	/**
	*
	*
	*/
	public function startElement($name){
		$node = new Node($name);			
		$this->current->addChild($node);
		$this->route[] = $this->current;
		$this->current = $node;
	}

	/**
	*
	*
	*/
	public function endElement(){
		if( count($this->route) < 1 ){
			$this->current = $this->head;
		}else{
			$this->current = array_pop($this->route);
		}
	}

	/**
	*
	*
	*/
	public function text($text){
		$this->current->text($text);
	}

	/**
	*
	*
	*/
	public function toString(){
		return print_r($this->iterate($this->head));
	}

	/**
	*
	*
	*/
	public function isHead(Node $node){
		return $node == $this->head;
	}

	/**
	*
	*
	*/
	public function iterate($current){		

		// leaf
		if( $current->isLeaf() )
			return array($current->name => $current->value);

		// branch
		$list = array();	
		foreach( $current->children as $child )
			$list = array_merge((array)$list, (array)$this->iterate($child));

		// Don't add root
		if( $this->isHead($current) )
			return $list;
		
		return array($current->name => $list);
	} 

	/**
	*
	*
	*/
	public function type(){
		return $this->type;
	}
}

/**
*
*
*/
class XML extends Tree {

	private $type = 'application/xml+';
	private $writer;

	/**
	*
	*
	*/
	public function __construct(){
		parent::__construct();
	}
	
	/**
	*
	*
	*/
	public function parse($data_str){
		libxml_use_internal_errors(true);
		$data = simplexml_load_string($data_str);
		if (!$data)
			throw new Exception("Unable to parse XML");
		return $data;
	}

	/**
	*
	*
	*/
	public function startDocument(){
		$this->create();
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');
		$this->writer->startDocument('1.0', 'UTF-8');		
	}

	/**
	*
	*
	*/
	public function endDocument(){
		$this->writer->endDocument();
		$this->end();
	}

	/**
	*
	*
	*/
	public function iterate($current){

		// Leaf
		if( $current->isLeaf() ){
			$this->writer->startElement($current->name);
			$this->writer->text($current->value);
			$this->writer->endElement();
			return;
		}

		// Don't add numeric indecies or root
		if( is_int($current->name) || $this->isHead($current) ){
			foreach( $current->children as $node )
				$this->iterate($node);
			return;
		}

		// Branch
		$this->writer->startElement($current->name);
		foreach( $current->children as $node )
			$this->iterate($node);
		$this->writer->endElement();
	} 

	/**
	*
	*
	*/
	public function toString(){
		//die(var_dump($this->head));
		$this->iterate($this->head);		
		return $this->writer->outputMemory();
	}

	/**
	*
	*
	*/
	public function type(){
		return $this->type;
	}
}

/**
*
*
*/
class YAML extends Tree {

	private $type = 'text/x-yaml+';

	/**
	*
	*
	*/
	public function __construct(){
		parent::__construct();
	}

	/**
	*
	*
	*/
	public function parse($data_str){
		$data = yaml_parse($data_str);
		return $data;
	}

	/**
	*
	*
	*/
	public function startDocument(){
		$this->create();
	}

	/**
	*
	*
	*/
	public function endDocument(){
		$this->end();
	}

	/**
	*
	*
	*/
	public function toString(){
		return yaml_emit($this->iterate($this->head));
	}

	/**
	*
	*
	*/
	public function type(){
		return $this->type;
	}
}

/**
*
*
*/
class JSON extends Tree {

	private $type = 'application/json+';

	/**
	*
	*
	*/
	public function __construct(){
		parent::__construct();
	}

	/**
	*
	*
	*/
	public function parse($data_str){
		$data = json_decode($data_str, true);
		return $data;
	}

	/**
	*
	*
	*/
	public function startDocument(){
		$this->create();
	}

	/**
	*
	*
	*/
	public function endDocument(){
		$this->end();
	}

	/**
	*
	*
	*/
	public function toString(){
		return json_encode($this->iterate($this->head));
	}

	/**
	*
	*
	*/
	public function type(){
		return $this->type;
	}
}

?>
