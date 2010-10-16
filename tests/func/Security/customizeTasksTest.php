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

class CustomizeTasks extends FForge_SeleniumTestCase
{
	function testCustomizeTasks()
	{
		$this->init();

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Add Task");
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
		$this->select("data[pmpublic][2]", "label=Private");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 2: Set the Tasks permission to No Access for a given role and a given subproject.
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[pm][2]", "label=No Access");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 3: Check that this subproject is not visible from a member associated with this role.
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->assertFalse($this->isTextPresent("To Do"));

		// Step 4: Set the permission to Read.
		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[pm][2]", "label=Read");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 5: Check that the member is able to see the subproject, but is not able to create
		// new tasks or modify existing tasks.
 		$this->switchUser("uuser");

 		$this->select("none", "label=projecta");
 		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->assertTextPresent("To Do");
		$this->clickAndWait("link=To Do");
		$this->assertTextPresent("Summary1");

		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");

		$this->clickAndWait("link=Add Task");
		$this->assertTextPresent("This project's administrator will have to grant you permission to view this page.");

		// Step 6: Set the permission to Tech.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[pm][2]", "label=Tech");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 7: Check that tasks can be assigned to the member.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Summary1");
		$this->removeSelection("assigned_to[]", "label=None");
		$this->addSelection("assigned_to[]", "label=uuser Lastname");
		$this->clickAndWait("submit");
  		$this->assertTextPresent("Task Updated Successfully");

		// Step 8: Set the permission to Tech and Admin.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[pm][2]", "label=Tech & Admin");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 9: Check that the member can be assigned to tasks by the project administrator,
		// is able to add new tasks and modify existing tasks.

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Add Task");
		$this->type("summary", "Summary2");
		$this->type("details", "Description2");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary2");
		$this->clickAndWait("link=Summary2");
		$this->assertTextPresent("Description2");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Summary2");
		$this->removeSelection("assigned_to[]", "label=None");
		$this->addSelection("assigned_to[]", "label=uuser Lastname");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Task Updated Successfully");

		$this->switchUser('uuser');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Add Task");
		$this->type("summary", "Summary3");
		$this->type("details", "Description3");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary3");
		$this->clickAndWait("link=Summary3");
		$this->assertTextPresent("Description3");

		// Step 10: Set the permission to Admin Only.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[pm][2]", "label=Tech & Admin");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 11: Check that the member can modify entries, but cannot be assigned to tasks.
		$this->switchUser('uuser');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Summary3");
		$this->select("priority", "label=5 - Highest");
		$this->select("end_day", "label=22");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Task Updated Successfully");

		// Step 12: Set the Observer privilege on the subproject to Public.

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[pmpublic][2]", "label=Public");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 13: Check that a non-member of the project can see the tasks, and but is not able
		// to modify them or create new ones.

		$this->switchUser('unonmember');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Tasks");
		$this->assertTextPresent("To Do");
		$this->clickAndWait("link=To Do");
		$this->assertTextPresent("Summary1");

		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");

		$this->clickAndWait("link=Add Task");
		$this->assertTextPresent("This project's administrator will have to grant you permission to view this page.");
	}
}
?>
