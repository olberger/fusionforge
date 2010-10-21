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

class CustomizeForums extends FForge_SeleniumTestCase
{
	function testCustomizeForums()
	{
		$this->init();

		$this->switchUser('uadmin');

		// Step 1: Select one project, and go to the Project Admin tab, then Users.
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[forumpublic][1]", "label=Private");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Members");

		// Step 2: Set to Forum permission to No Access for a given role and a given forum.
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[forum][1]", "label=No Access");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 3: Check that this forum is not visible from a member associated to this role.
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->assertFalse($this->isTextPresent("open-discussion"));

		// Step 4: Set the permission to Read.
		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[forum][1]", "label=Read");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 5: Check that the forum is visible for the user, but that the user is not allowed 
		// to start new threads or reply to messages.
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->assertTextPresent("This project's administrator will have to grant you permission to post in this forum.");

		// Step 6: Set the permission to Post.
		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[forum][1]", "label=Post");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 7: Check that the user is able to post a message in the forum, but is not allowed to administrate the forum.
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "My Subject");
		$this->type("body", "Hello World!");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->assertTextPresent("My Subject");
		$this->assertFalse($this->isElementPresent("link=Administration"));
		
		// Step 8: Set the permission to Admin.
		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[forum][1]", "label=Admin");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 9: Check that the user is able to administrate the forum.
		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "My Subject");
		$this->type("body", "Hello World!");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->assertTextPresent("My Subject");
		$this->assertTrue($this->isElementPresent("link=Administration"));
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=open-discussion");
		$this->click("is_public");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Forum Info Updated Successfully");

		// Step 10: Set the Observer privilege on the forum to Public, No Anonymous Posts.
		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 11: Check that a logged user, non member of the project, is able to see
		// the forum and to post messages, but is not allowed to administrate the forum.
		$this->switchUser("unonmember");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "I am not a member of the project.");
		$this->type("body", "Hello World!");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->assertTextPresent("I am not a member of the project.");
		$this->assertFalse($this->isElementPresent("link=Administration"));

		// Step 12: Check that a user not logged in is not able to post messages.
		$this->logout();

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->assertTextPresent("Please enter your Corporate Short Login and Password");

		// Step 13: Set the Observer privilege on the forum to Public, Allow Anonymous Posts.
		$this->login("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=open-discussion");
		$this->click("allow_anonymous");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Forum Info Updated Successfully");

		// Step 14: Check that a non logged user is able to post messages.
		$this->logout();

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "I am not logged in.");
		$this->type("body", "Hello World!");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->assertTextPresent("I am not logged in.");
		$this->assertFalse($this->isElementPresent("link=Administration"));
	}
}
?>
