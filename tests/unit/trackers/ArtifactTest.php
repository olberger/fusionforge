<?php

require_once 'PHPUnit/Framework.php';
require_once $gfcommon.'include/Group.class.php';
require_once $gfcommon.'tracker/Artifact.class.php';

class ArtifactTest extends PHPUnit_Framework_TestCase {

	protected $group, $at, $a;

	protected function setUp()
	{
		db_query_params('TRUNCATE user_session', array());
		
		$this->group = new Group(6);
		$this->at = new ArtifactType($this->group, 102);
		$this->a = new Artifact($this->at);
	}
	
	function testCreateAsAnonymousOnNonAnonymousTracker() 
	{
		$this->assertFalse($this->a->create("I (anonymous) have a problem", "Here is a detailed status of"));
	}
	
	function testCreateASimpleTracker()
	{
		$id = $this->createSimpleTicket("I (admin) have a problem", "Here is a detailed status of");
		$this->assertTrue($id > 0);
		
		$body = $this->a->sendBodyMsg;
		$this->assertTrue( strpos($body, 'Status: Open') !== false); 
		$this->assertTrue( strpos($body, 'Summary: I (admin) have a problem') !== false);
		$this->assertTrue( strpos($body, 'Hardware: None') !== false);
		$this->assertTrue( strpos($body, 'Here is a detailed status of') !== false);

		$this->assertEquals( $this->a->getID(), $id);

		$res = db_query_params('SELECT user_id FROM user_group WHERE group_id=1', array());
		$admin_id = db_result($res,0,'user_id');
		$this->assertEquals( $this->a->getSubmittedBy(), $admin_id);

		$this->assertEquals( $this->a->getPriority(), 3);
		$this->assertEquals( $this->a->getAssignedTo(), 100);
		$this->assertEquals( $this->a->getCloseDate(), 0);

		$this->a->delete(1);
	}
	
	function testUpdateSimpleTracker()
	{
		$id = $this->createSimpleTicket("I (admin) have a problem", "Here is a detailed status of");
		$this->assertTrue($id > 0);
		
		$a = artifact_get_object($id);
		$a->update(5, 1, 100, "I (admin) have a problem", 100, "Comment added", $a->ArtifactType->getID());

		$this->assertEquals( $a->getPriority(), 5);

		$body = $a->sendBodyMsg;
		$this->assertTrue( strpos($body, '>Priority: 5') !== false); 
		$this->assertTrue( strpos($body, '>Comment By:') !== false); 
		
		$a->delete(1);
	}
	
	function testUpdateTrackerWithHtmlWords()
	{
		$id = $this->createSimpleTicket("With a > in subject", "Here is a detailed status of");
		$this->assertTrue($id > 0);
		
		$a = artifact_get_object($id);
		$a->update(3, 1, 100, "With a > in subject", 100, "Comment added", $a->ArtifactType->getID());

		$body = $a->sendBodyMsg;
		$this->assertFalse( strpos($body, '>Priority:') !== false); 
		$this->assertFalse( strpos($body, '>Summary:') !== false); 
		
		$a->delete(1);
	}
	
	function createSimpleTicket($subject, $message)
	{
		$res = db_query_params('SELECT user_id FROM user_group WHERE group_id=1', array());
		session_set_new(db_result($res,0,'user_id'));

		return $this->a->create($subject, $message);
	}
}
?>
