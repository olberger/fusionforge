<?php
/**
 * squal_pre.php
 *
 * Drastically simplified version of pre.php
 * 
 * Sets up database connection and session
 * 
 * NOTE:
 *		You cannot call HTML-related functions
 * 
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 */

require_once $gfcommon.'include/config.php';

ini_set('memory_limit', '32M');

if (!isset($no_gz_buffer) || !$no_gz_buffer) {
    ob_start("ob_gzhandler");
}

require_once('local.inc');
require_once $gfcommon.'include/config-vars.php';

require('common/include/constants.php');
require_once('common/include/database.php');
require_once('common/include/session.php');
require_once('common/include/Error.class.php');
require_once('common/include/User.class.php');
require_once('common/include/Permission.class.php');
require_once('common/include/utils.php');
require_once('common/include/Group.class.php');
require_once('common/include/escapingUtils.php');

// Plugins subsystem
require_once('common/include/Plugin.class.php') ;
require_once('common/include/PluginManager.class.php') ;

//plain text version of exit_error();
require_once('squal_exit.php');

//require_once('browser.php');

//system library
require_once $gfcommon.'include/System.class.php';
forge_define_config_item('account_manager_type', 'core', 'UNIX') ;
require_once $gfcommon.'include/system/'.forge_get_config('account_manager_type').'.class.php';
$amt = forge_get_config('account_manager_type') ;
$SYS = new $amt();


// #### Connect to db

db_connect();

if (!$conn) {
	exit_error("Could Not Connect to Database",db_error());
}

// #### set session

// SCM-specific plugins subsystem
require_once('common/include/SCM.class.php') ;

setup_plugin_manager () ;

session_set();

?>
