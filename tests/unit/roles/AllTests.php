<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'unit_roles_AllTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once dirname(__FILE__).'/RoleTest.php';

class roles_AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Unit roles');

		$suite->addTestSuite('RoleTest');
		
		return $suite;
	}
}

if (PHPUnit_MAIN_METHOD == 'unit_roles_AllTests::main') {
	Framework_AllTests::main();
}
?>