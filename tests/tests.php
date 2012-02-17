<?php
require_once('/usr/share/php5/simpletest/autorun.php');
require_once('/usr/share/php5/simpletest/unit_tester.php');
require_once('/usr/share/php5/simpletest/mock_objects.php');

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');
		/* Test Library code */

		/* Test Models & views */
		$this->addFile('/var/www/exerciser/tests/viewTest.php');
		$this->addFile('/var/www/exerciser/tests/activityTest.php');
		$this->addFile('/var/www/exerciser/tests/activitiesTest.php');
		$this->addFile('/var/www/exerciser/tests/workoutTest.php');
		$this->addFile('/var/www/exerciser/tests/workoutsTest.php');
		$this->addFile('/var/www/exerciser/tests/userTest.php');
		$this->addFile('/var/www/exerciser/tests/usersTest.php');
		//$this->addFile('/var/www/exerciser/tests/statsTest.php');

		/* Test Resources */
		//$this->addFile('/var/www/exerciser/tests/activityResourceTest.php');
		//$this->addFile('/var/www/exerciser/tests/activitiesResourceTest.php');
		//$this->addFile('/var/www/exerciser/tests/workoutResourceTest.php');
		//$this->addFile('/var/www/exerciser/tests/workoutsResourceTest.php');
		//$this->addFile('/var/www/exerciser/tests/userResourceTest.php');
		//$this->addFile('/var/www/exerciser/tests/usersResourceTest.php');
		//$this->addFile('/var/www/exerciser/tests/exerciserResourceTest.php');
	}
}


?>
