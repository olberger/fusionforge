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

class CreateRelease extends FForge_SeleniumTestCase
{
	function testCreateRelease()
	{
		$this->init();
		
		// Create a package:
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("link=Create a New Release");
		$this->clickAndWait("link=create a new package");
		$this->type("document.forms[3].package_name", "L'année dernière à Noël");
		$this->clickAndWait("document.forms[3].submit");
		$this->clickAndWait("link=File Release System");
		$this->assertTextPresent("L'année dernière à Noël");
		
		// Create one release with a link for package.
		//  - Release link1 for the projecta package
                // Do not provide "File Type" and "Processor Type"
		$this->clickAndWait("link=Create a New Release");
		$this->type("release_name", "L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->type("userlink", URL.ROOT."www/projecta/index.html");
		$this->select("package_id", "label=projecta");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=File Release System");

		// TEST1: Access using the admin account
		// Admin is allowed to see & get all the files.

		// Check that all packages are visible for the admin user.
		$this->clickAndWait("link=File Release System");
		$this->assertTextPresent("L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		
		// Check that link1 is accessible to admin user.
		$this->open( ROOT .'');
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("//a[contains(@href, '/frs/download.php/1/index.html')]");
		$this->assertTextPresent("This project has not created its website yet!");
	}
}
?>
