<?php

require_once 'PHPUnit/Framework.php';
require_once $gfcommon.'include/Group.class.php';
require_once $gfcommon.'include/Role.class.php';

class RoleTest extends PHPUnit_Framework_TestCase {

	protected $group;

	protected function setUp()
	{
		db_query_params("TRUNCATE user_session", array());

		$this->group = new Group(6);
	}
	
	function testDeleteRole()
	{
		// No delete if no admin rights.
		$role = new Role($this->group, 23);
		$ret = $role->delete();
		$this->assertFalse($ret);
		$this->assertEquals('Permission denied.', $role->getErrorMessage());

		$res = db_query_params('SELECT user_id FROM user_group WHERE group_id=1', array());
		session_set_new(db_result($res,0,'user_id'));
		
		// No delete if role has members inside.
		$role = new Role($this->group, 23);
		$ret = $role->delete();
		$this->assertFalse($ret);
		$this->assertEquals('Cannot remove a non empty role.', $role->getErrorMessage());
		
		// No delete for the default role (role_id=1)
		$role = new Role($this->group, 1);
		$ret = $role->delete();
		$this->assertFalse($ret);
		$this->assertEquals('Cannot Delete Default Role.', $role->getErrorMessage());

		// Bad id, no delete
		$role = new Role($this->group, 'bad');
		$ret = $role->delete();
		$this->assertFalse($ret);
		
		// Delete ok for a normal role.
		$role = new Role($this->group);
		$role->defaults['Tester'] = $role->defaults['Admin'];
		$role_id = $role->createDefault('Tester');
		$this->assertTrue($role_id > 0);
		$ret = $role->delete();
		$this->assertTrue($ret);
		
		// Cannot delete an already deleted role.
		$ret = $role->delete();
		$this->assertFalse($ret);
	}
}
?>
