<?php
// $Id: RPC2.php 7638 2010-08-11 11:58:40Z vargenau $
/*
 * The guts of this code have been moved to lib/XmlRpcServer.php.
 *
 * This file is really a vestige, as now, you can direct XML-RPC
 * request to the main wiki URL (e.g. index.php) --- it will
 * notice that you've POSTed content-type of text/xml and
 * fire up the XML-RPC server automatically.
 */

// Intercept GET requests from confused users.  Only POST is allowed here!
if (empty($GLOBALS['HTTP_SERVER_VARS']))
    $GLOBALS['HTTP_SERVER_VARS']  =& $_SERVER;
if ($HTTP_SERVER_VARS['REQUEST_METHOD'] != "POST")
{
    die('This is the address of the XML-RPC interface.' .
        '  You must use XML-RPC calls to access information here.');
}

// Constant defined to indicate to phpwiki that it is being accessed via XML-RPC
define ("WIKI_XMLRPC", true);

// Start up the main code
include_once("index.php");
include_once("lib/main.php");

include_once("lib/XmlRpcServer.php");

$server = new XmlRpcServer;
$server->service();

// Local Variables:
// mode: php
// tab-width: 8
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End: 
?>
