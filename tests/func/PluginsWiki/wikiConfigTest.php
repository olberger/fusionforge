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

class WikiConfig extends FForge_SeleniumTestCase
{
	function testwikiConfig()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		// Step 1: create page with word in CamelCase. Check that it is not a link: "?" is NOT present.
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "I do no like CamelCase.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("CamelCase");
		$this->assertFalse($this->isTextPresent("exact:CamelCase?"));

		// Step 2: change wiki config so that CamelCase is enabled.
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Tools");
		$this->clickAndWait("link=Wiki Admin");
		$this->click("config[DISABLE_MARKUP_WIKIWORD]");
		$this->clickAndWait("//input[@value='Save Configuration']");

		// Step 3: create page with word in CamelCase. Check that it is a link: "?" is present.
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "I like CamelCase.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("exact:CamelCase?");
		$this->clickAndWait("link=?");
		$this->assertTextPresent("Describe");

		// Test anti-spam prevention

		// Step 4: add many external links as admin
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "== Anti spam ==\n\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/");
		$this->click("edit-summary");
		$this->type("edit-summary", "Anti spam");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("http://acos.alcatel-lucent.com");
		$this->assertFalse($this->isTextPresent("Spam Prevention"));

		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "Home Page");
		$this->click("edit-summary");
		$this->type("edit-summary", "Reset Home Page");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		// Step 5: add many external links as ucontrib
		$this->switchUser('ucontrib');

		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "== Anti spam ==\n\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/");
		$this->click("edit-summary");
		$this->type("edit-summary", "Anti spam");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("http://www.alcatel-lucent.com");
		$this->assertFalse($this->isTextPresent("Spam Prevention"));

		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "Home Page");
		$this->click("edit-summary");
		$this->type("edit-summary", "Reset Home Page");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		$this->switchUser('admin');

		// Step 6: change wiki config so that anti-spam is enabled.
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Tools");
		$this->clickAndWait("link=Wiki Admin");
		$this->click("config[NUM_SPAM_LINKS]");
		$this->clickAndWait("//input[@value='Save Configuration']");

		// Step 7: add many external links as admin
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "== Anti spam ==\n\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/\n* http://acos.alcatel-lucent.com/");
		$this->click("edit-summary");
		$this->type("edit-summary", "Anti spam");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("http://acos.alcatel-lucent.com");
		$this->assertFalse($this->isTextPresent("Spam Prevention"));

		// Step 8: add many external links as ucontrib
		$this->switchUser('ucontrib');

		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");

		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "Home Page");
		$this->click("edit-summary");
		$this->type("edit-summary", "Reset Home Page");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "== Anti spam ==\n\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/\n* http://www.alcatel-lucent.com/");
		$this->click("edit-summary");
		$this->type("edit-summary", "Anti spam");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("Spam Prevention");
	}
}
?>
