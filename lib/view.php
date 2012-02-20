<?php

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Node contains basic text along with a refference to children. For use in Tree class
 * 	@namespace Exerciser\View
 */
class Node {

	public $name;
	public $value;
	public $children = array();

	/**
	 * Constructer for node
	 * @param String optional node name
	 */
	public function __construct($name=''){
		$this->name = $name;
	}

	/**
	 * Adds node text
	 * @param String node text
	 */
	public function text($text){
		$this->value = (string)$text;
	}

	/**
	 * Adds a child to the Node
	 * @param Node a child Node
	 */
	public function addChild(Node $child){
		$this->children[] = $child;
	}

	/**
	 * Checks if the node is a leaf.
	 * @return Boolean True if node has no children
	 */
	public function isLeaf(){
		return count($this->children) == 0;
	}
}

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	Tree class contains a N-ary tree of nodes for the purposes of building a document
 * 	@namespace Exerciser\View
 */
class Tree {

	public $head;
	private $current;
	private $route = array();
	protected $type = '';	

	/**
	 * Constructor, creates a new empty Tree
	 */
	public function __construct(){
		$this->create();
	}

	/**
	 * Checks if Tree is empty
	 * @return Boolean True is tree is empty
	 */
	public function isEmpty(){
		return $this->head == NULL;
	}
	
	/**
	 * Initialises the Tree
	 */
	public function create(){
		$this->head = NULL;
		$this->current = $this->head;
		$this->route = array();		
	}

	/**
	 * Adds a new node as a child of the current Node
	 * @param String the Node name
	 */
	public function startElement($name){
		$node = new Node($name);
		if( $this->isEmpty() ){
			$this->head = $node;
		} else{		
			$this->current->addChild($node);
			$this->route[] = $this->current;
		}
		$this->current = $node;
	}

	/**
	 * Finishes addition to current Node
	 */
	public function endElement(){
		if( count($this->route) < 1 ){
			$this->current = $this->head;
		}else{
			$this->current = array_pop($this->route);
		}
	}

	/**
	 * Adds text to the current node.
	 * Text is only visible on Leaf nodes
	 * @param String the node text
	 */
	public function text($text){
		$this->current->text($text);
	}

	/**
	 * Turns the current Tree document into a text representation of the array.
	 * @return String containing the document array
	 */
	public function toString(){
		return print_r($this->iterate($this->head));
	}

	/**
	 * Checks if a Node is the root
	 * @return Boolean True if the specified node is the Root Node
	 */
	public function isHead($node){
		return $node == $this->head;
	}

	/**
	 * Parses a tree like array into the Tree
	 * @param Array of values to be processed
	 */
	protected function process($data){
		foreach($data as $key => $value)
			if( is_array($value) ){				
				$this->startElement($key);
				$this->process($value);
				$this->endElement();
			}else{
				$this->startElement($key);
				$this->text($value);
				$this->endElement();
			}	
	}

	/**
	 * Converts the tree to an array with a tree like structure
	 * @return array containing all nodes in tree structure
	 */
	public function getArray(){
		return $this->iterate($this->head);
	}

	/**
	 * Iterates through the children of the specified Node
	 * Builds an Array structure recursively
	 * @param Node to iterate
	 */
	public function iterate($current){
		if( is_null($current) )
			return;

		// leaf
		if( $current->isLeaf() )
			return array($current->name => $current->value);

		// branch
		$list = array();	
		foreach( $current->children as $child )
			$list = array_merge((array)$list, (array)$this->iterate($child));
		
		return array($current->name => $list);
	} 

	/**
	 * Gets the type of document being processed
	 * @return String the type
	 */
	public function type(){
		return $this->type;
	}
}

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	XML document view
 * 	@namespace Exerciser\View
 */
class XML extends Tree {

	protected $type = 'application/xml+';
	private $writer;

	/**
	 * Creates a new Document, creating a new instance of Tree
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Parses the document string into the Tree
	 * @param String containing the document
	 */
	public function parse($data_str){
		if (!$data_str)
			throw new Exception("Unable to parse JSON");

		libxml_use_internal_errors(true);
		$data = simplexml_load_string($data_str);
		if (!$data)
			throw new Exception("Unable to parse XML");

		$this->startElement($data->getName());
		$this->process($data);
		$this->endElement();		
	}

	/**
	 * Creates a new document Tree
	 */
	public function startDocument(){
		$this->create();			
	}

	/**
  	 * Recursively iterates through the specified Node building an XML document
	 * @param Node the node to iterate
	 */
	public function xmlIterate(Node $current){

		if( is_null($current) )
			return;

		// Leaf
		if( $current->isLeaf() ){
			$this->writer->startElement($current->name);
			$this->writer->text($current->value);
			$this->writer->endElement();
			return;
		}

		// Don't add numeric indecies
		if( is_int($current->name) ){
			foreach( $current->children as $node )
				$this->xmlIterate($node);
			return;
		}

		// Branch
		$this->writer->startElement($current->name);
		foreach( $current->children as $node )
			$this->xmlIterate($node);
		$this->writer->endElement();
	} 

	/**
	 * Converts the Tree into a document
	 * @return String containing the document
	 */
	public function toString(){
		$this->writer = new \XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->setIndentString(' ');
		$this->writer->startDocument('1.0', 'UTF-8');	
		$this->xmlIterate($this->head);
		$this->writer->endDocument();	
		return $this->writer->outputMemory();
	}
}

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	YAML document view
 * 	@namespace Exerciser\View
 */
class YAML extends Tree {

	protected $type = 'text/x-yaml+';

	/**
	 * Creates a new Document, creating a new instance of Tree
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Parses the document string into the Tree
	 * @param String containing the document
	 */
	public function parse($data_str){
		if (!$data_str)
			throw new Exception("Unable to parse JSON");

		$data = yaml_parse($data_str);

		if (!$data)
			throw new Exception("Unable to parse YAML");
		$this->process($data);
	}

	/**
	 * Creates a new document Tree
	 */
	public function startDocument(){
		$this->create();
	}

	/**
	 * Converts the Tree into a document
	 * @return String containing the document
	 */
	public function toString(){
		return yaml_emit($this->getArray());
	}
}

/**
 *	@author Martin Buckley - MBuckley@gmail.com
 *	JSON document view
 * 	@namespace Exerciser\View
 */
class JSON extends Tree {

	protected $type = 'application/json+';

	/**
	 * Creates a new Document, creating a new instance of Tree
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Parses the document string into the Tree
	 * @param String containing the document
	 */
	public function parse($data_str){
		if (!$data_str)
			throw new Exception("Unable to parse JSON");

		$data = json_decode($data_str, true);

		if (!$data)
			throw new Exception("Unable to parse JSON");
		$this->process($data);
	}

	/**
	 * Creates a new document Tree
	 */
	public function startDocument(){
		$this->create();
	}

	/**
	 * Converts the Tree into a document
	 * @return String containing the document
	 */
	public function toString(){
		return json_encode($this->getArray());
	}
}

?>
