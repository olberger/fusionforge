<?php
/*
 * Copyright (C) 2010 Alcatel-Lucent
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

class CustomizeTrackers extends FForge_SeleniumTestCase
{
	function testCustomizeTrackers()
	{
		$this->init();

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");

		// Step 1: Select one project, and go to the Project Admin tab, then Users.
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[trackerpublic][101]", "label=Private");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 2: Set the Tracker permission to No Access for a given role and a given tracker.
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[tracker][101]", "label=No Access");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 3: Check that this tracker is not visible from a member associated with this role
		// (check access via the browser and via CLI).
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->assertFalse($this->isTextPresent("Bugs"));

		// Step 4: Set the permission to Read.
		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[tracker][101]", "label=Read");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 5: Check that the member is able to see the tracker, but is not able to submit
		// new entries or modify existing entries (check access via the browser and via CLI).
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent("Summary1");

		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");

		$this->clickAndWait("link=Submit New");
		$this->assertTextPresent("This project's administrator will have to grant you permission to post in this tracker.");

		// Step 6: Set the permission to Tech.
		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[tracker][101]", "label=Tech");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 7: Check that tracker items can be assigned to the member.
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1");
		$this->select("assigned_to", "label=uuser Lastname");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully updated");

		// Step 8: Set the permission to Tech & Admin.
		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=Core Developer");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[tracker][101]", "label=Tech & Admin");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 9: Check that the member can be assigned to entries by the project administrator,
		// is able to create new entries and modify existing entries.
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1");
		$this->select("assigned_to", "label=ucoredev Lastname");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully updated");

		$this->switchUser('ucoredev');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Created by Tech Admin");
		$this->type("details", "Hello boys");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Created by Tech Admin");
		$this->clickAndWait("link=Created by Tech Admin");
		$this->assertTextPresent("Hello boys");

		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1");
		$this->select("extra_fields[6]", "label=blocker");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully updated");

		// Step 10: Set the permission to Admin Only.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=Contributor");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[tracker][101]", "label=Admin Only");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 11: Check that the member can modify entries, but cannot be assigned to tracker items.

		$this->switchUser('ucontrib');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1");
		$this->select("extra_fields[6]", "label=critical");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully updated");

		// Step 12: Set the Observer privilege on the tracker to Public, No Anonymous Posts.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[trackerpublic][101]", "label=Public");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 13: Check that non-member of the project can see the tracker, and can submit new entries
		// (check access via the browser and via CLI).

		$this->switchUser('unonmember');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Created by non member of project");
		$this->type("details", "Hello girls");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Created by non member of project");
		$this->clickAndWait("link=Created by non member of project");
		$this->assertTextPresent("Hello girls");

		// Step 14: Check that a user not logged-in is not able to submit entries.

		$this->logout();

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent("Summary1");

		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");

		$this->clickAndWait("link=Submit New");
		$this->assertTextPresent("Please enter your Corporate Short Login and Password");

		// Step 15: Set the Observer privilege on the tracker to Public, Allow Anonymous Posts.

		$this->login("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[trackerpublic][101]", "label=Public");
		$this->select("data[trackeranon][101]", "label=Allow Anonymous Posts");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 16: Check that a user not logged in is able to submit new entries to the tracker
		// (check access via the browser and via CLI).

		$this->logout();

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Created by anonymous user");
		$this->type("details", "Hello everyone");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Created by anonymous user");
		$this->clickAndWait("link=Created by anonymous user");
		$this->assertTextPresent("Hello everyone");
	}
}
?>
