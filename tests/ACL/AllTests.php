<?php
require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/ACLTests.php';

class ACL_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');

		$suite->addTestSuite('ACL_Tests');

		return $suite;
	}
}
?>
