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

class AddCustomField extends FForge_SeleniumTestCase
{
	function testAddCustomField()
	{
		$this->init('ProjectA', 'uadmin');

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Manage Custom Fields");
		$this->type("name", "Fixed in Release");
		$this->click("//input[@name='field_type' and @value='4']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Customize Browse List");
		$this->type("browse_fields[22]", "5");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Tracker Updated");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->assertTextPresent("Fixed in Release:");
		$this->type("summary", "MyTicketSummary");
		$this->type("details", "Detailed description");
		$this->click("extra_fields[22]");
		$this->type("extra_fields[22]", "V1.4");
		$this->clickAndWait("//div[@id='maindiv']/form/table/tbody/tr[14]/td/input");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent("Fixed in Release");
		$this->clickAndWait("link=MyTicketSummary");
		$this->assertTextPresent("Fixed in Release:");

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Manage Custom Fields");
		$this->type("name", "checkbox");
		$this->click("//input[@name='field_type' and @value='2']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->type("name", "radio");
		$this->click("//input[@name='field_type' and @value='3']");
		$this->clickAndWait("post_changes");
		$this->clickAndWait("//div[@id='maindiv']/table/tbody/tr[8]/td[4]/a");
		$this->type("name", "one");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");
		$this->type("name", "two");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");
		$this->type("name", "FM");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");
		$this->type("name", "AM");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");
		$this->type("name", "LW");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Customize Browse List");
		$this->type("browse_fields[23]", "6");
		$this->type("browse_fields[24]", "7");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Tracker Updated");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent("checkbox");
		$this->assertTextPresent("radio");
		$this->assertTextPresent("LW");
		$this->assertTextPresent("two");
	}
}
?>
