<?php

require_once(LIB . 'MultiViewResource.php');

/*
 * @namespace Exerciser\Tests\Lib
 */
class TestOfMultiViewResource extends UnitTestCase {

	private $config = array();
	private $uri = '/';
	private $res;

	protected static function getMethod($name) {
		$class = new ReflectionClass('MultiViewResource');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	function setup() {		
		$this->config = array('baseUri' => URI);
		$this->res = new MultiViewResource($this->config);
	}

	function testSecured() {
		$username = 'user';
		$password = 'pass';
		$method = self::getMethod('isSecured');
		$this->expectException('ResponseException');
		$method->invokeArgs($this->res, array($username, $password));

		$_SERVER['PHP_AUTH_USER'] = $username;
		$_SERVER['PHP_AUTH_PW'] = $password;
		$method->invokeArgs($this->res, array($username, $password));
	}

	function testViews() {
		$config = array();
		$this->assertEqual($this->get_view($config), 'XML');
		$config = array('uri' => '.xml');
		$this->assertEqual($this->get_view($config), 'XML');
		$config = array('uri' => '.json');
		$this->assertEqual($this->get_view($config), 'JSON');
		$config = array('uri' => '.yaml');
		$this->assertEqual($this->get_view($config), 'YAML');
	}

	private function get_view($config) {
		$request = new Request($config);
		$method = self::getMethod('get_view');
		$view = $method->invokeArgs($this->res, array($request));
		return get_class($view);
	}

}

?>
