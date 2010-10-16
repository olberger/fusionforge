<?php

require_once 'PHPUnit/Framework.php';
require_once $gfcommon.'include/Group.class.php';
require_once $gfcommon.'include/Plugin.class.php';
require_once $gfcommon.'include/PluginManager.class.php';
require_once $gfcommon.'include/SCM.class.php';

class SvnHudsonTest extends PHPUnit_Framework_TestCase {

	protected $group, $at, $a;

	protected function setUp()
	{
		db_query_params("TRUNCATE user_session", array());

		require($GLOBALS['sys_plugins_path'].'svnhudson/common/svnhudson-init.php');
	
		$this->pg = $svnhudsonPluginObject;
		
//		$this->group = new Group(6);
//		$this->at = new ArtifactType($this->group, 102);
//		$this->a = new Artifact($this->at);
	}
	
	function testPluginName() 
	{
		$this->assertEquals($this->pg->name, 'svnhudson');
	}
}
?>