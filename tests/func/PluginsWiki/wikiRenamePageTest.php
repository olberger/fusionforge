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

class WikiRenamePage extends FForge_SeleniumTestCase
{
	function testwikiRenamePage()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		// Step 1: select page with history
		// Create "toto" with history
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{colorbox\n|text=<strong>Welcome to the wiki dedicated to your project!</strong>\n|color=#90EE90\n|bordercolor=green\n}}\n\nI create [[toto]].");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->clickAndWait("link=exact:?");
		$this->type("edit-content", "This is a new page.");
		$this->type("edit-summary", "This is a new page.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "This is a new page.\n\nI add a new paragraph.");
		$this->type("edit-summary", "I add a new paragraph.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		// Step 2: select Wiki Admin link
		$this->clickAndWait("link=Wiki Admin");

		// Step 3: enter name of page to rename
		$this->type("s", "toto");

		// Step 4: click WikiAdminSelect button
		$this->clickAndWait("//input[@value='WikiAdminSelect']");

		// Step 4: click Rename button
		$this->clickAndWait("wikiadmin[WikiAdminRename]");

		// Step 5: enter new page name
		$this->type("admin_rename[to]", "titi");

		// Step 6: click Rename
		$this->clickAndWait("admin_rename[rename]");

		// Step 7: click Yes
		$this->clickAndWait("admin_rename[rename]");

		// Step 8: access page under new name
		$this->clickAndWait("link=titi");

		// Step 9: redo test with Rename tab
		$this->clickAndWait("link=titi");
		$this->clickAndWait("link=Rename");
		$this->type("admin_rename[to]", "tata");
		$this->clickAndWait("admin_rename[rename]");
		$this->clickAndWait("admin_rename[rename]");
		$this->clickAndWait("link=tata");
		$this->clickAndWait("link=History");

		// Check page history
		$this->assertTextPresent("This is a new page.");
		$this->assertTextPresent("I add a new paragraph.");
		$this->assertTextPresent("Renamed page from 'titi' to 'tata'");

		// Step 10: redo test with redirect checked
		$this->clickAndWait("link=tata");
		$this->clickAndWait("link=Rename");
		$this->type("admin_rename[to]", "tutu");
		$this->click("admin_rename-createredirect");
		$this->clickAndWait("admin_rename[rename]");
		$this->clickAndWait("admin_rename[rename]");
		$this->clickAndWait("link=tutu");
		$this->clickAndWait("link=History");

		// Check page history
		$this->assertTextPresent("This is a new page.");
		$this->assertTextPresent("I add a new paragraph.");
		$this->assertTextPresent("Renamed page from 'tata' to 'tutu'");

		// Check redirection works
		$this->clickAndWait("link=Recent Changes");
		$this->clickAndWait("link=tata");
		$this->assertTextPresent("(Redirected from tata)");
		$this->assertTextPresent("This is a new page.");
		$this->assertTextPresent("I add a new paragraph.");
	}
}
?>
