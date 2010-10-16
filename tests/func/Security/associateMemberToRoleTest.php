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

class AssociateMemberToRole extends FForge_SeleniumTestCase
{
	function testAssociateMemberToRole()
	{

		$this->init();

		$this->createUser('unewuser', 110);

		$this->switchUser('uadmin');

		// Step 1: Select the Project Admin section of the project.
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");

		// Step 2: Check that the default roles proposed are: 
		// Admin, Core Developer, Contributor, Community Support Assistant, and User.
		$this->clickAndWait("link=Members");
		$this->assertTextPresent("Admin");
		$this->assertTextPresent("Core Developer");
		$this->assertTextPresent("Contributor");
		$this->assertTextPresent("Community Support Assistant");
		$this->assertTextPresent("User");

		// Step 3: Add a user and associate this user to a role.
		$this->type("form_unix_name", "unewuser");
		$this->select("role_id", "label=User");
		$this->clickAndWait("adduser");
		$this->assertTextPresent("Member Added Successfully");

		// Step 4: Log-in as user and check that the project is listed in My Page section,
		// and that the user with the role associated is listed in the Project Summary 
		// page of the project.
		$this->switchUser('unewuser');

		$this->clickAndWait("link=My Page");
		$this->assertTextPresent("ProjectA");
		$this->clickAndWait("link=ProjectA");
		$this->assertTextPresent("unewuser Lastname");
	}
}
?>
