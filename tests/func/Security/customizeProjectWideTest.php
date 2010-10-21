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

class CustomizeProjectWide extends FForge_SeleniumTestCase
{
	function testCustomizeProjectWide()
	{
		$this->init();

		$this->switchUser('uadmin');

		// Step 1: Select one project, and go to the Project Admin section.
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");

		// Step 2: For a given role, change the project-wide admin permission to Admin. 
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=Core Developer");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[projectadmin][0]", "label=Admin");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 3: Log-in as the user associated to this role, and check that the Project Admin tab for this project is available.
		$this->switchUser('ucoredev');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Project Admin");
		$this->clickAndWait("link=Project Admin");
		$this->assertTextPresent("Project Information");

		// Step 4: Log-in as project administrator and set the permission back to None.
		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=Core Developer");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[projectadmin][0]", "label=None");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		$this->switchUser('ucoredev');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->assertEquals("Project Activity", $this->getText("//td[@id='leftnav']/table/tbody/tr[2]/td"));
	}
}
?>
