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

class AssignToMe extends FForge_SeleniumTestCase
{
	function testAssignToMe()
	{
		$this->init();

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "My Summary");
		$this->click("//form[@id='tracker-add']/table/tbody/tr[10]/td/strong");
		$this->type("details", "My Detailed description");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Item [#1] successfully created");
		$this->clickAndWait("link=My Summary");
		$this->assertTextPresent("My Detailed description");
		$this->clickAndWait("//div[@id='maindiv']/form/table[1]/tbody/tr/td[4]/a/strong");
		$this->assertTextPresent("Tracker [#1] is now assigned to you");
		$this->clickAndWait("//div[@id='maindiv']/form/table[1]/tbody/tr/td[2]/a/strong");
		$this->clickAndWait("add_to_task");
		$this->assertTextPresent("Exiting with error");
		$this->assertTextPresent("No Available Tasks Found");

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=My Summary");
		$this->clickAndWait("//div[@id='maindiv']/form/table[1]/tbody/tr/td[2]/a/strong");
		$this->clickAndWait("new_task");
		$this->type("details", "Task Details");
		$this->clickAndWait("submit");

		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Add Task");
		$this->type("summary", "My new task");
		$this->type("details", "My new task details.");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=My Summary");
		$this->clickAndWait("//div[@id='maindiv']/form/table[1]/tbody/tr/td[2]/a/strong");
		$this->clickAndWait("add_to_task");
		$this->clickAndWait("done_adding");
		$this->assertTextPresent("Successfully Added Tracker Relationship");
	}
}
?>
