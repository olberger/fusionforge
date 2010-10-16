<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

$root = dirname(dirname( __FILE__));

set_include_path(get_include_path() . PATH_SEPARATOR . '/etc/gforge' . PATH_SEPARATOR . $root.'/src' .
	PATH_SEPARATOR . "/usr/share/php");

$gfcommon = $root.'/src/common/';
$gfwww    = $root.'/src/www/';

$GLOBALS['sys_dbhost']='localhost';
$GLOBALS['sys_dbname']= 'gforge';
$GLOBALS['sys_dbuser']= 'gforge';
$GLOBALS['sys_dbpasswd']= '';

$GLOBALS['sys_path_to_jpgraph'] = '/vz/private/203/opt/jpgraph/';

$GLOBALS['sys_urlroot'] = $root.'/www';
$GLOBALS['sys_session_key'] = 'test';
$GLOBALS['sys_session_expire'] = 0;
$GLOBALS['sys_default_domain'] = 'localhost.localdomain';

$GLOBALS['sys_var_path'] = '/var';
$GLOBALS['sys_install_type'] = 'development';
$GLOBALS['sys_plugins_path'] = '/opt/gforge/plugins/';

$GLOBALS['sys_use_ssl'] = false;
$GLOBALS['sys_urlprefix'] = '';

require_once $gfcommon.'include/config.php';
require_once $gfcommon.'include/config-vars.php';
require_once $gfcommon.'include/database-pgsql.php';
require_once $gfcommon.'include/session.php';
require_once $gfcommon.'include/utils.php';
require_once $gfcommon.'include/Permission.class.php';

db_connect();

if (!$GLOBALS['gfconn']) {
	print "Could Not Connect to Database".db_error()."\n";
}

// Load extra func to add extras func like debug
if (!isset($no_debug) && $sys_install_type == 'development' || $sys_install_type == 'integration') {
	require $gfcommon.'include/extras-debug.php';
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// Unit tests
require_once 'Utils/AllTests.php';
require_once 'unit/trackers/AllTests.php';
require_once 'unit/svnhudson/AllTests.php';
require_once 'unit/roles/AllTests.php';

// Code tests
require_once 'code/syntax/AllTests.php';

// Exclude tests directorie.
PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__));

class AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPUnit');

		// Unit tests
		$suite->addTest(Utils_AllTests::suite());
		$suite->addTest(Trackers_AllTests::suite());
		$suite->addTest(SvnHudson_AllTests::suite());
		$suite->addTest(Roles_AllTests::suite());
		
		// Code tests
		$suite->addTest(Syntax_AllTests::suite());
		
		return $suite;
	}
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
	AllTests::main();
}
?>
