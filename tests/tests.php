<?php
require_once('/usr/share/php5/simpletest/autorun.php');
require_once('/usr/share/php5/simpletest/unit_tester.php');
require_once('/usr/share/php5/simpletest/mock_objects.php');

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');
		$this->addFile('/var/www/exerciser/tests/activityTest.php');
		$this->addFile('/var/www/exerciser/tests/activityResourceTest.php');
	}
}


?>
