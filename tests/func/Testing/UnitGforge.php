<?php

set_include_path(".:/opt/gforge/:/opt/gforge/www/include/:/etc/gforge/:".get_include_path());

require_once 'config.php';
require_once 'PHPUnit/Framework/TestCase.php';

class UnitGForge extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		global $sys_urlroot, $sys_dbhost, $sys_dbport, $sys_dbname, $sys_dbuser, $sys_dbpasswd,
			$sys_ldap_passwd, $sys_jabber_pass, $sys_server, $conn,
			$sys_var_path, $sys_plugins_path, $sys_session_key, $sys_session_expire, $session_hash,
			$sys_use_shell, $sys_shell_host, $sys_scm_host,
			$sys_default_domain, $sys_upload_dir, $sys_lists_host, $sys_name, $SYS,
			$_sys_db_transaction_level, $sys_default_timezone, $sys_default_theme_id,
			$sys_require_unique_email;

		// Reload a fresh database before running this test suite.
		system("php ".dirname(dirname(__FILE__))."/db_reload.php");

		require_once 'pre_for_tests.php';
	}
	
	function createProject($name)
	{
		$res = db_query("SELECT user_id FROM user_group WHERE group_id=1");
		$admin_id = db_result($res,0,'user_id');
		session_set_new($admin_id);

		$user = user_get_object_by_name('admin');
		
		$scm = 'websvn';
		$plugin = plugin_get_object($scm);
		$scm_host = $plugin->getDefaultServer();
		
		$group = new Group();
		$ret = $group->create($user, $name, $name, "this is the description of $name",
			100, '', "The purpose of to test the forge", 'shell1', $scm_host);
		$group->setPluginUse($scm,true);
		$ret = $group->approve($user);
		$group_id = $group->getID();
		
		$hook_params = array () ;
		$hook_params['group_id'] = $group_id ;
		plugin_hook ("group_approved", $hook_params) ;
		
		return $group_id;
	}
	
	/**
	 * Create a user with the given login.
	 * 
	 * @param $login			The unix login.
	 * @param $external		boolean	0 if internal, 1 if external.
	 * @return user_id if success, false if failed.	
	 */
	function createUser($login, $external=0)
	{
		$user = new GFUser();
		$lastname = $external ? 'Lastname (external)' : 'Lastname';
		$user_id = $user->create($login, $login, $lastname, 'password', 'password', $login.'@debug.log', false, false,
			1 , $GLOBALS['sys_default_timezone'], '', false, $GLOBALS['sys_default_theme_id']);
		if (!$user_id) {
			return false;
		}
		$res = $user->setStatus('A');
		if (!$res) {
			return false;
		}
		if ($external) {
			$res = $user->setIsExternal(1);
			if (!$res) {
				return false;
			}
		}
		return $user_id;
	}
}

?>
