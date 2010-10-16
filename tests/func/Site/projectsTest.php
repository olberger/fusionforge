<?php
/*
 * Copyright (C) 2008 Alain Peyrat <aljeux@free.fr>
 * Copyright (C) 2009 Alain Peyrat, Alcatel-Lucent
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * FusionForge is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FusionForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */

/*
 * Standard Alcatel-Lucent disclaimer for contributing to open source
 *
 * "The test suite ("Contribution") has not been tested and/or
 * validated for release as or in products, combinations with products or
 * other commercial use. Any use of the Contribution is entirely made at
 * the user's own responsibility and the user can not rely on any features,
 * functionalities or performances Alcatel-Lucent has attributed to the
 * Contribution.
 *
 * THE CONTRIBUTION BY ALCATEL-LUCENT IS PROVIDED AS IS, WITHOUT WARRANTY
 * OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, COMPLIANCE,
 * NON-INTERFERENCE AND/OR INTERWORKING WITH THE SOFTWARE TO WHICH THE
 * CONTRIBUTION HAS BEEN MADE, TITLE AND NON-INFRINGEMENT. IN NO EVENT SHALL
 * ALCATEL-LUCENT BE LIABLE FOR ANY DAMAGES OR OTHER LIABLITY, WHETHER IN
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * CONTRIBUTION OR THE USE OR OTHER DEALINGS IN THE CONTRIBUTION, WHETHER
 * TOGETHER WITH THE SOFTWARE TO WHICH THE CONTRIBUTION RELATES OR ON A STAND
 * ALONE BASIS."
 */

require_once dirname(dirname(__FILE__)).'/Testing/SeleniumGforge.php';

class CreateProject extends FForge_SeleniumTestCase
{
	// Simple creation of a project by the admin user and
	// approval of the creation just after.
	// After creation, project is visible on the main page.
	function testSimpleCreate()
	{

		// Test with a correct Unix name
		$prj = "projectb";

		$this->open( ROOT );
		$this->waitForPageToLoad("30000");

		$this->login('admin');

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", $prj);
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Your project has been submitted");
		$this->assertTextPresent("you will receive notification of their decision and further instructions");
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Pending (P) (New Project Approval)");
		$this->clickAndWait("document.forms['approve.$prj'].submit");
		$this->clickAndWait("link=Home");
		$this->assertTextPresent("$prj");
		$this->clickAndWait("link=$prj");
		$this->assertTextPresent("This is the public description for $prj.");
		$this->assertTextPresent("This project has not yet categorized itself");

		// Test with an empty Unix name
		$prj = "";

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "Full Name");
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Invalid Unix name");

		// Test with a too short Unix name
		$prj = "ab";

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "Full Name");
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Invalid Unix name");

		// Test with a Unix name containing a space
		$prj = "wrong name";

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "Full Name");
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Invalid Unix name");

		// Test with a Unix name containing a dot
		$prj = "foo.bar";

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "Full Name");
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertFalse($this->isTextPresent("Invalid Unix name"));

		// Test with a Unix name containing a quote
		$prj = "foo'bar";

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "Full Name");
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Invalid Unix name");

		// Test with a Unix name containing illegal characters
		$prj = "père";

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "Full Name");
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Invalid Unix name");
	}

	function testCharsCreateTestCase()
	{
		$prj = "projectb";

		$this->open( ROOT );
		$this->waitForPageToLoad("30000");

		$this->login('admin');

		$this->clearMail();

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", "$prj ' & Z");
		$this->type("purpose", "This is a & été simple description for $prj");
		$this->type("description", "This is & été the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Your project has been submitted");
		$this->assertTextPresent("you will receive notification of their decision and further instructions");
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Pending (P) (New Project Approval)");
		$this->clickAndWait("document.forms['approve.$prj'].submit");
		$this->clickAndWait("link=Home");
		$this->assertTextPresent("$prj ' & Z");
		$this->clickAndWait("link=$prj ' & Z");
		$this->assertTextPresent("This is & été the public description for $prj.");

		// Verify generated mail
		$mailRef = file_get_contents(dirname(__FILE__).'/mail1.txt');
		$mailRef = str_replace('{site}', SITE, $mailRef);
		$mail = $this->fetchMail();
		$this->assertEquals($mailRef, $mail);
	}

	// Test removal of project.
	// TODO: Test not finished as removal does not work.
	function testRemoveProject()
	{
		$this->createProject('testal1');

		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Display Full Project List/Edit Projects");
		$this->clickAndWait("link=testal1");
		$this->clickAndWait("link=Permanently Delete Project");
		$this->click("sure");
		$this->click("reallysure");
		$this->click("reallyreallysure");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Project successfully deleted");
	}

	// Simple creation of a project by a normal user and
	// approval of the creation just after.
	// After creation, project is visible on the main page.
	function testCreateWithAUser()
	{
		$prj = "projectb";

		$this->open( ROOT );
		$this->login('admin');

		$this->createUser('uadmin', 103);
		$this->switchUser('uadmin');

		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", $prj);
		$this->type("purpose", "This is a simple description for $prj");
		$this->type("description", "This is the public description for $prj.");
		$this->type("unix_name", $prj);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Your project has been submitted");
		$this->assertTextPresent("you will receive notification of their decision and further instructions");

		$this->switchUser('admin');

		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Pending (P) (New Project Approval)");
		$this->clickAndWait("document.forms['approve.$prj'].submit");
		$this->clickAndWait("link=Home");
		$this->assertTextPresent("$prj");
		$this->clickAndWait("link=$prj");
		$this->clickAndWait("link=Project Summary");
		$this->clickAndWait("link=View the 1 Member(s)");
		$this->assertTextPresent("Admin");
		$this->assertFalse($this->isTextPresent("Default"));
	}

	function testApproveTemplateProject()
	{
		$this->open( ROOT );

		$this->login('admin');

		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Pending (P) (New Project Approval)");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Approving Project: template");
		$this->assertTextPresent("No Pending Projects to Approve");
	}
}
?>
