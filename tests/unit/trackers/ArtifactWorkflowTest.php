<?php

require_once 'PHPUnit/Framework.php';
require_once $gfcommon.'include/Group.class.php';
require_once $gfcommon.'tracker/ArtifactType.class.php';
require_once $gfcommon.'tracker/ArtifactWorkflow.class.php';

class ArtifactWorkflowTest extends PHPUnit_Framework_TestCase {

	protected $atw;

	protected function setUp()
	{
		db_query_params('TRUNCATE user_session', array());

		$group = new Group(6);
		$ath = new ArtifactType($group,'101');
		$efarr =& $ath->getExtraFields(ARTIFACT_EXTRAFIELDTYPE_STATUS);
		if (empty($efarr)) {
			$res = db_query_params('SELECT user_id FROM user_group WHERE group_id=1', arrray());
			$admin_id = db_result($res,0,'user_id');
			session_set_new($admin_id);
			
			$ab = new ArtifactExtraField($ath);
			if (!$ab || !is_object($ab) || $ab->isError()) {
				$this->fail('Unable to create ArtifactExtraField Object');
			} else {
				if (!$ab->create("MyStatus",ARTIFACT_EXTRAFIELDTYPE_STATUS,0,0)) {
					$this->fail('Unable to create ArtifactExtraField Object: '. $ab->getErrorMessage());
					$ab->clearError();
				}
			}
			$ath = new ArtifactType($group,'101');
			$efarr =& $ath->getExtraFields(ARTIFACT_EXTRAFIELDTYPE_STATUS);
		}
		$keys=array_keys($efarr);
		$field_id = $keys[0];
		$this->atw = new ArtifactWorkflow($ath, $field_id);
	}
	
	function testCheckEvent() 
	{
		$this->assertTrue($this->atw->checkEvent(100, 100));
		
		$this->atw->removeNode(101);
		$this->atw->addNode(101);
		$this->assertTrue($this->atw->checkEvent(100, 101));
	}
	
	function testNextNodes()
	{
		$next = $this->atw->getNextNodes(100);
		$this->assertTrue( in_array(101, $next));
		
		$this->assertTrue( $this->atw->saveNextNodes(100, $next) );
		$next2 = $this->atw->getNextNodes(100);
		$this->assertEquals($next, $next2);
	}

	function testAllowedRoles()
	{
		$roles = $this->atw->getAllowedRoles(100, 101);
		$this->assertTrue( $this->atw->saveAllowedRoles(100, 101, $roles));
		$roles2 = $this->atw->getAllowedRoles(100, 101);
		$this->assertEquals($roles, $roles2);
	}
}
?>
