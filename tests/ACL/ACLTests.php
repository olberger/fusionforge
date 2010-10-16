<?php

require_once 'Testing/UnitGforge.php';

/**
 * Simple math test class.
 *
 * @package   Example
 * @author    Manuel Pichler <mapi@phpundercontrol.org>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 0.4.7
 * @link      http://www.phpundercontrol.org/
 */
class ACL_Tests extends UnitGForge
{
	/**
	 * Test the generated rules by the acl class.
	 */
	public function testGeneratedRules()
	{
		$group_id = $this->createProject('projecta');
	
		// test1: Check default rules.
		$acl = new Acl(1, 0);
		$acl->writeAllRules();

		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test1_svnaccess.txt", $val);

		$val = file_get_contents("/etc/gforge/http/davroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test1_davaccess.txt", $val);
		
		// test2: Add rule on /toto, with admin:rw, public and anon.
		$pacl = new Acl($group_id, 1);
		$pacl->addRule('/toto', array(22 => 1), 1, 1);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}

		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test2_svnaccess.txt", $val);

		$res = db_query_params('SELECT acl_id FROM acl WHERE group_id=$1 AND val=$2', array($group_id, '/toto'));
		$acl_id = db_result($res, 0, 'acl_id');
		
		// test3: Update rule on /toto, with admin:rw, public and -anon.
		$pacl->updateRule($acl_id, array(22 => 1), 1, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}

		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test3_svnaccess.txt", $val);

		// test4: Update rule on /toto, with admin:rw, -public and -anon.
		$pacl->updateRule($acl_id, array(22 => 1), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}

		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test4_svnaccess.txt", $val);

		// test5: Update rule on /toto, with admin:r, -public and -anon.
		$pacl->updateRule($acl_id, array(22 => 0), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}

		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test5_svnaccess.txt", $val);

		// test6: Update rule on /toto, with admin:-, -public and -anon.
		$pacl->updateRule($acl_id, array(22 => -1), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}

		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test6_svnaccess.txt", $val);
		
		// Now, add an external user to the project, and check the rules.
		$group = group_get_object_by_name('projecta');
		$ret = $group->setIsExternal(true);
		$this->assertTrue(true, "Setting project external");
		
		// Add an external user to the project.
		$user_id = $this->createUser('external', 1);
		$this->assertEquals($user_id, 103);

		$role = new Role($group);

		$rights = array();	
		$rights['projectadmin'][0] = 0;
		$rights['frs'][0] = 0;
		$rights['frspackage'][0] = 0;
		$rights['scm'][0] = 0;
		$rights['dav'][0] = 0;
		$rights['docman'][0] = 0;
		$rights['forumadmin'][0] = 0;
		$rights['forum'][0] = 0;
		$rights['trackeradmin'][0] = 0;
		$rights['tracker'][0] = 0;
		$rights['pmadmin'][0] = 0;
		$rights['pm'][0] = 0;
		$role_id = $role->create('External-Orange', $rights);
		$role->setIsExternal(1);
		
		$group->addUser('external', $role_id);

		// Now, setup is correct, check the generated file.
		$val = file_get_contents("/etc/gforge/http/svnroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test1_ext_svnaccess.txt", $val);

		$val = file_get_contents("/etc/gforge/http/davroot-access");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test1_ext_davaccess.txt", $val);
		
//		$val = file_get_contents("/etc/gforge/http/svnroot-access-external");
//		$this->assertStringEqualsFile(dirname(__FILE__)."/test1_ext_svnaccess_external.txt", $val);

//		$val = file_get_contents("/etc/gforge/http/davroot-access-external");
//		$this->assertStringEqualsFile(dirname(__FILE__)."/test1_ext_davaccess_external.txt", $val);

    	// Add rw for this role on /third-parties
		$pacl->addRule('/third-parties', array(22 => 1, $role_id => 1), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}
		
		$val = file_get_contents("/etc/gforge/http/svnroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test2_ext_svnaccess_external.txt", $val);

		$val = file_get_contents("/etc/gforge/http/davroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test2_ext_davaccess_external.txt", $val);		

    	// Add rw for this role on /public => Not possible
    	// So the rule is not in the access files, same result as previous.
    	// @alu: Maybe creation of the rule should be forbidden
		$pacl->addRule('/public', array(22 => 1, $role_id => 1), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}
		
		$val = file_get_contents("/etc/gforge/http/svnroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test2_ext_svnaccess_external.txt", $val);

		$val = file_get_contents("/etc/gforge/http/davroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test2_ext_davaccess_external.txt", $val);		

    	// Add read access for this role on /third-parties/secret
		$pacl->addRule('/third-parties/read', array(22 => 1, $role_id => 0), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}
		
		$val = file_get_contents("/etc/gforge/http/svnroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test3_ext_svnaccess_external.txt", $val);

		$val = file_get_contents("/etc/gforge/http/davroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test3_ext_davaccess_external.txt", $val);

		// Add no access for this role on /third-parties/secret
		$pacl->addRule('/third-parties/secret', array(22 => 1, $role_id => -1), 0, 0);
		if ($pacl->isError()) {
			print $pacl->getErrorMessage();
		}
		
		$val = file_get_contents("/etc/gforge/http/svnroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test4_ext_svnaccess_external.txt", $val);

		$val = file_get_contents("/etc/gforge/http/davroot-access-external");
		$this->assertStringEqualsFile(dirname(__FILE__)."/test4_ext_davaccess_external.txt", $val);
	}
}
