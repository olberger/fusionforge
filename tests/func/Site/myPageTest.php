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

class MyPage extends FForge_SeleniumTestCase
{
	function testmyPage()
	{
	$this->init();

	$this->switchUser("ucoredev");
	$this->select("none", "label=projecta");
	$this->waitForPageToLoad("30000");
	$this->clickAndWait("link=Trackers");
	$this->clickAndWait("link=Bugs");
	$this->clickAndWait("link=Submit New");
	$this->type("summary", "This is a bug");
	$this->type("details", "Detailed description.");
	$this->clickAndWait("submit");

	$this->switchUser("uadmin");
	$this->select("none", "label=projecta");
	$this->waitForPageToLoad("30000");
	$this->clickAndWait("link=Trackers");
	$this->clickAndWait("link=Bugs");
	$this->clickAndWait("link=Submit New");
	$this->type("summary", "This is a bug assigned to ucoredev");
	$this->select("assigned_to", "label=ucoredev Lastname");
	$this->click("//option[@value='107']");
	$this->type("details", "Detailed description");
	$this->clickAndWait("submit");

	$this->switchUser("ucoredev");
	$this->select("none", "label=projecta");
	$this->waitForPageToLoad("30000");
	$this->clickAndWait("link=Tasks");
	$this->clickAndWait("link=To Do");
	$this->clickAndWait("link=Add Task");
	$this->type("summary", "This is my first task");
	$this->type("details", "Task Details");
	$this->clickAndWait("submit");

	$this->switchUser("uadmin");
        $this->select("none", "label=projecta");
        $this->waitForPageToLoad("30000");
	$this->clickAndWait("link=Tasks");
	$this->clickAndWait("link=To Do");
	$this->clickAndWait("link=Add Task");
	$this->type("summary", "This is a task assigned to ucoredev");
	$this->type("details", "Task Details");
	$this->removeSelection("assigned_to[]", "label=None");
	$this->addSelection("assigned_to[]", "label=ucoredev Lastname");
	$this->clickAndWait("submit");

	$this->switchUser("ucoredev");
	$this->select("none", "label=projecta");
	$this->waitForPageToLoad("30000");
	$this->clickAndWait("link=Forums");
	$this->clickAndWait("link=open-discussion");
	$this->clickAndWait("link=Monitor Forum");
	$this->assertTextPresent("Forum monitoring started");
	$this->clickAndWait("link=My Page");

	$this->assertTextPresent("ProjectA - Bugs");
	$this->assertTextPresent("This is a bug assigned to ucoredev");
	$this->assertTextPresent("This is a task assigned to ucoredev");
	$this->assertTextPresent("This is a bug");
	$this->assertTextPresent("open-discussion");
	$this->assertTextPresent("You are not monitoring any trackers.");
	$this->assertTextPresent("You are not monitoring any Forge News. ");
	$this->assertTextPresent("You are not monitoring any files.");
    }
}
?>
