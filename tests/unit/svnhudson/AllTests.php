<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'unit_svnhudson_AllTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once dirname(__FILE__).'/SvnHudsonTest.php';

class SvnHudson_AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Unit trackers');

		$suite->addTestSuite('SvnHudsonTest');
		
		return $suite;
	}
}

if (PHPUnit_MAIN_METHOD == 'unit_svnhudson_AllTests::main') {
	SvnHudson_AllTests::main();
}
?>