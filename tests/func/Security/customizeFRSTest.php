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

class CustomizeFRS extends FForge_SeleniumTestCase
{
	function testCustomizeFRS()
	{
		$this->init();

		$this->switchUser('uadmin');

		// Step 1: Select one project, and go to the Project Admin section.
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");

		// Step 2: For a given role, set the File Release System permission to Read and None.
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[frs][0]", "label=None");
		$this->select("data[frspackage][1]", "label=Read");
		$this->clickAndWait("submit");

		// Step 3: Log-in as a project member associated to this role.
		$this->switchUser('uuser');

		// Step 4: Check that the member has read access to the File Release System section (the member cannot create new releases).
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=File Release System");
		$this->assertFalse($this->isTextPresent("Create a New Release"));

		// Step 5: Set the File Release System permission to Write.
		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[frs][0]", "label=None");
		$this->select("data[frspackage][1]", "label=Write");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		// Step 6: Check that the member is allowed to create new releases.
		$this->switchUser('uuser');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=File Release System");
		$this->assertTextPresent("Below is a list of all files of the project. Before downloading, you may want to read Release Notes and Change Log.");
		$this->clickAndWait("link=Create a New Release");
		$this->type("release_name", "New Release");
		$this->type("userlink", "http://www.fusionforge.org");
		$this->clickAndWait("submit");
		$this->assertTextPresent("File Released: You May Choose To Edit the Release Now");
	}
}
?>
