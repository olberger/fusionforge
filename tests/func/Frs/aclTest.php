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

class FrsAcl extends FForge_SeleniumTestCase
{
	function testFrsAcl()
	{
		$this->init();
		// Create 2 packages:
		//   package_public: public by default.
		//   package_private: private by default.
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("link=Create a New Release");
		$this->clickAndWait("link=create a new package");
		$this->type("document.forms[3].package_name", "package_public");
		$this->clickAndWait("document.forms[3].submit");
		$this->type("document.forms[4].package_name", "package_private");
		$this->click("document.forms[4].is_public[1]");
		$this->clickAndWait("document.forms[4].submit");
		$this->clickAndWait("link=File Release System");
		
		// Create one release with a link for each package.
		//  - Release link1 for the projecta package
		//  - Release link2 for the project_public package
		//  - Release link3 for the project_private package
		$this->clickAndWait("link=Create a New Release");
		$this->type("release_name", "link1");
		$this->type("userlink", URL.ROOT."www/projecta/index.html");
		$this->select("type_id", "label=html");
		$this->select("processor_id", "label=Other");
		$this->type("release_notes", "Release note for link1");
		$this->type("release_changes", "Changelog for link1");
		$this->select("package_id", "label=projecta");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=File Release System");

		$this->clickAndWait("link=Create a New Release");
		$this->type("release_name", "link2");
		$this->type("userlink", URL.ROOT."www/projecta/index.html");
		$this->select("type_id", "label=html");
		$this->select("processor_id", "label=Other");
		$this->type("release_notes", "Release note for link2");
		$this->type("release_changes", "change log for link2");
		$this->select("package_id", "label=package_public");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=File Release System");

		$this->clickAndWait("link=Create a New Release");
		$this->type("release_name", "link3");
		$this->type("userlink", URL.ROOT."www/projecta/index.html");
		$this->select("type_id", "label=html");
		$this->select("processor_id", "label=Other");
		$this->type("release_notes", "Release note for link3 (");
		$this->select("package_id", "label=package_private");
		$this->type("release_notes", "Release note for link3 (private)");
		$this->type("release_changes", "Change log for link3");
		$this->clickAndWait("submit");
		
		// TEST1: Access using the admin account
		// Admin is allowed to see & get all the files.

		// Check that all packages are visible for the admin user.
		$this->clickAndWait("link=File Release System");
		$this->assertTextPresent("link1");
		$this->assertTextPresent("link2");
		$this->assertTextPresent("link3");
		
		// Check that link1 is accessible to admin user.
		$this->open( ROOT .'');
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("//a[contains(@href, '/frs/download.php/1/index.html')]");
		$this->assertTextPresent("This project has not created its website yet!");
		
		// Check that link2 is accessible to admin user.
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("//a[contains(@href, '/frs/download.php/2/index.html')]");
		$this->assertTextPresent("This project has not created its website yet!");
		
		// Check that link3 is accessible to admin user.
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("link=index.html");
		$this->assertTextPresent("This project has not created its website yet!");

		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		
		// Test downloading the available releases.
		// nnoaccess user is not allowed to see and get the files.
		$this->switchUser('unoaccess');

		// TEST2: Access using the noaccess account
		// noaccess is not allowed to see & get all the files.
		// TODO: BUT as projecta & project_public are public, he can access them.

		// Check that link3 (private) packages is not visible for the noaccess user.
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->assertTextPresent("link1");
		$this->assertTextPresent("link2");
		$this->assertFalse($this->isTextPresent("link3"));
		
		// Check that link1 is accessible to noaccess user (package is public).
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("//a[contains(@href, '/frs/download.php/1/index.html')]");
		$this->assertTextPresent("This project has not created its website yet!");
		
		// Check that link2 is accessible to noaccess user (package is public).
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("//a[contains(@href, '/frs/download.php/2/index.html')]");
		$this->assertTextPresent("This project has not created its website yet!");
		
		// Check that link3 is not accessible to noaccess user (private & noaccess).
		$this->open( ROOT . '/frs/download.php/3/index.html');
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Permission denied.");
		$this->assertFalse($this->isTextPresent("This project has not created its website yet!"));

		// Check that link1 & link2 are displayed on the frontpage
		// But link3 is not displayed (private & noaccess) for this user.
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->assertTextPresent("link1");
		$this->assertTextPresent("link2");
		$this->assertFalse($this->isTextPresent("link3"));
		
	}
}
?>
