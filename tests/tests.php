<?php
require_once('/usr/share/php5/simpletest/autorun.php');
require_once('/usr/share/php5/simpletest/unit_tester.php');
require_once('/usr/share/php5/simpletest/web_tester.php');
require_once('/usr/share/php5/simpletest/mock_objects.php');
require_once('/var/www/exerciser/config.php');

define('TEST_CLASSES', ROOT . 'tests/classes/', true);
define('TEST_LIB', ROOT . 'tests/lib/', true);
define('TEST_RESOURCES', ROOT . 'tests/resources/', true);


/*
 * @namespace Exerciser\Tests
 */
class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');

		/* Test Library code */
		$this->addFile('/var/www/exerciser/tests/lib/viewTest.php');
		$this->addFile('/var/www/exerciser/tests/lib/CRUDTest.php');
		$this->addFile('/var/www/exerciser/tests/lib/DBATest.php');

		/* Test Models & views */		
		$this->addFile('/var/www/exerciser/tests/classes/activityTest.php');
		$this->addFile('/var/www/exerciser/tests/classes/activitiesTest.php');
		$this->addFile('/var/www/exerciser/tests/classes/workoutTest.php');
		$this->addFile('/var/www/exerciser/tests/classes/workoutsTest.php');
		$this->addFile('/var/www/exerciser/tests/classes/userTest.php');
		$this->addFile('/var/www/exerciser/tests/classes/usersTest.php');
		$this->addFile('/var/www/exerciser/tests/classes/statsTest.php');

		/* Test Resources */
		$this->addFile('/var/www/exerciser/tests/resources/multiViewResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/activityResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/activitiesResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/workoutResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/workoutsResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/userResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/usersResourceTest.php');
		$this->addFile('/var/www/exerciser/tests/resources/exerciserResourceTest.php');
	}
}


?>
