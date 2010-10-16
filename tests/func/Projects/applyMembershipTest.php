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

class ApplyMembership extends FForge_SeleniumTestCase
{
  function testApplyMembership()
  {
    $this->init();

    // "unonmember" tries to join with an empty form
    $this->switchUser('unonmember');
    $this->open("/projects/projecta/");
    $this->clickAndWait("link=Project Summary");
    $this->clickAndWait("link=Request to join");
    $this->clickAndWait("submit");
    $this->assertTextPresent("Exiting with error");
    $this->assertTextPresent("Must include comments");

    // "uuser" tries to join, but is already member
    // $this->switchUser('uuser');
    // $this->open("/projects/projecta/");
    // $this->click("link=Project Summary");
    // $this->waitForPageToLoad("30000");
    // $this->click("link=Request to join");
    // $this->waitForPageToLoad("30000");
    // $this->type("comments", "I would like to join the project.");
    // $this->click("submit");
    // $this->waitForPageToLoad("30000");
    // $this->assertTextPresent("Error");
    // $this->assertTextPresent("You are already a member of this project.");

    // "uadmin" removes "uuser" from project
    $this->switchUser('uadmin');
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Project Admin");
    $this->clickAndWait("link=Members");
    $this->clickAndWait("//td[@id='main']/table/tbody/tr/td[2]/table/tbody/tr[2]/td/form[7]/table/tbody/tr/td[2]/input[2]");
    $this->assertTextPresent("Member Removed Successfully");

    // "uuser" tries to join
    $this->switchUser('uuser');
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Request to join");
    $this->type("comments", "I would like to join your project.");
    $this->clickAndWait("submit");
    $this->assertTextPresent("Your request has been submitted.");

    // "uadmin" accepts "uuser"
    $this->switchUser('uadmin');
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Project Admin");
    $this->clickAndWait("link=Members");
    $this->assertTextPresent("Pending Membership Requests");
    $this->select("role_id", "label=User");
    $this->clickAndWait("acceptpending");
    $this->assertTextPresent("Member Added Successfully");
    $this->clickAndWait("link=Members");
    $this->assertFalse($this->isTextPresent("Pending Requests"));

    // unlogged user tries to join project
    $this->clickAndWait("link=Log Out");
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Request to join");
    $this->assertTextPresent("Please enter your Corporate Short Login and Password");
    $this->type("form_loginname", "uuser");
    $this->type("form_pw", "password");
    $this->clickAndWait("login");
    $this->assertTextPresent("You can request to join a project by clicking the submit button. The administrators will be emailed to approve or deny your request.");

    // "uadmin" adds block 
    $this->logout();
    $this->activatePlugin('blocks');
    $this->login('uadmin');
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Project Admin");
    $this->clickAndWait("link=Tools");
    $this->click("use_blocks");
    $this->clickAndWait("submit");
    $this->assertTextPresent("Project information updated");
    $this->clickAndWait("link=Blocks Admin");
    $this->click("activate[request_join]");
    $this->clickAndWait("//input[@value='Save Blocks']");
    $this->clickAndWait("//td[@id='main']/form/table/tbody/tr[2]/td[4]/a");
    $this->type("body", "{boxHeader}Welcome to our project!{boxFooter}");
    $this->clickAndWait("//input[@value='Save']");

    // "unonmember" sees block 
    $this->switchUser('unonmember');
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Request to join");
    $this->assertTextPresent("Welcome to our project!");
  }
}
?>
