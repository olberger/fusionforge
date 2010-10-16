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

class CustomizeDocuments extends FForge_SeleniumTestCase
{
	function testCustomizeDocuments()
	{
		$this->init();

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		$this->switchUser('uuser');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Documents");
		$this->clickAndWait("link=Submit new");
		$this->type("title", "My document");
		$this->type("description", "My document description");
		$this->type("file_url", "http://www.fusionforge.org");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Document has been successfully uploaded and is waiting to be approved.");

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Documents");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Uncategorized Submissions");
		$this->clickAndWait("link=My document");
		$this->select("stateid", "label=active");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Updated successfully");

		$this->switchUser('uuser');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Documents");
		$this->click("link=Uncategorized Submissions");
		$this->assertTextPresent("My document description");

		$this->switchUser('uadmin');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//td[@id='main']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[docman][0]", "label=Admin");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");

		$this->switchUser('uuser');

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Documents");
		$this->assertEquals("Administration", $this->getText("link=Administration"));
		$this->clickAndWait("link=Submit new");
		$this->type("title", "My document2");
		$this->type("description", "My document2 description");
		$this->type("file_url", "http://www.fusionforge.org");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Document submitted sucessfully");
		$this->assertTextPresent("My document2 description");
	}
}
?>
