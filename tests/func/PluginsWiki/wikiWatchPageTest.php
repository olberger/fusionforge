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

class WikiWatchPage extends FForge_SeleniumTestCase
{
	function testwikiWatchPage()
	{
		$this->init();
		$this->activateWiki();

		// Open homepage on the wiki.
		$this->open(ROOT."/wiki/g/projecta/HomePage");
		$this->waitForPageToLoad("30000");

		// Step 1: In the Wiki section, select User Preferences
		$this->clickAndWait("link=User Preferences");

		// Step 2: Enter a page name in the box
		$this->type("pref[notifyPages]", "HomePage");

		// Step 3: Click Update Preferences.
		$this->clickAndWait("//input[@value='Update Preferences']");

		$this->clearMail();

		// Step 4: Modifiy the given wiki page.
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "admin was here.");
		$this->type("edit-summary", "comment of admin");
		$this->clickAndWait("edit[save]");

		// Step 5: Check that an e-mail message is received, and that it contains the modifications of the page.
		$mail = $this->fetchMail();
		$pos = strstr($mail, '+ admin was here.');
		$this->assertTrue($pos !== false);

		// Step 6: Select User Preferences
		$this->clickAndWait("link=User Preferences");

		// Step 7: Suppress the page name in the box
		$this->type("pref[notifyPages]", "");

		// Step 8: Click Update Preferences.
		$this->clickAndWait("//input[@value='Update Preferences']");

		// Step 9: Modify the wiki page again.
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "admin has gone.");
		$this->type("edit-summary", "comment of admin");
		$this->clickAndWait("edit[save]");

		// Step 10: Check that no e-mail message is received.
		$this->assertFalse($this->getMail());

		// Step 11: Select Preferences in the Navigation menu.
		$this->clickAndWait("link=User Preferences");

		// Step 12: Enter a '*' in the box
		$this->type("pref[notifyPages]", "*");

		// Step 13: Click Update Preferences.
		$this->clickAndWait("//input[@value='Update Preferences']");

		// Step 14: Modifiy several wiki pages.
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "admin creates [[PageOne]].");
		$this->type("edit-summary", "comment of admin");
		$this->clickAndWait("edit[save]");

		$mail = $this->fetchMail();

		$pos = strstr($mail, 'Subject: [projecta] Page change HomePage');
		$this->assertTrue($pos !== false);

		$pos = strstr($mail, '+ admin creates [[PageOne]].');
		$this->assertTrue($pos !== false);

		$this->clickAndWait("link=exact:?");
		$this->type("edit-content", "admin edits [[PageOne]].");
		$this->type("edit-summary", "comment of admin");
		$this->clickAndWait("edit[save]");

		// Step 15: Check that an e-mail message is received, and that it contains the modifications of the pages.
		$mail = $this->fetchMail();

		$pos = strstr($mail, 'Subject: [projecta] Page change PageOne');
		$this->assertTrue($pos !== false);

		$pos = strstr($mail, 'New page');
		$this->assertTrue($pos !== false);

		// Step 16: Select User Preferences
		$this->clickAndWait("link=User Preferences");

		// Step 17: Suppress the page name in the box
		$this->type("pref[notifyPages]", "");

		// Step 18: Click Update Preferences.
		$this->clickAndWait("//input[@value='Update Preferences']");

		// Step 19: Modifiy wiki pages again.
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "admin is mad.");
		$this->type("edit-summary", "nocomment ;-)");
		$this->clickAndWait("edit[save]");

		// Step 20: Check that no e-mail message is received.
		$this->assertFalse($this->getMail());

		// Step 21: Redo with multiple wikis.
		// Create another project 'projectb' and monitor a page on this wiki.
		// Then check that an email is receive about projectb (but not a).
		$this->logout();

		$this->createProject('ProjectB');
		// Activate the wiki plugin on projectB
		$this->activateWiki('ProjectB');

		// Open homepage on the wiki.
		$this->clearMail();

		$this->open(ROOT."/wiki/g/projectb/HomePage");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=User Preferences");
		$this->type("pref[notifyPages]", "HomePage");
		$this->clickAndWait("//input[@value='Update Preferences']");
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "admin was on projectb.");
		$this->type("edit-summary", "comment of admin");
		$this->clickAndWait("edit[save]");

		$mail = $this->fetchMail();

		// No emails about projecta
		$pos = strstr($mail, 'Subject: [projecta] Page change HomePage');
		$this->assertTrue($pos === false);

		// An email about projectb
		$pos = strstr($mail, 'Subject: [projectb] Page change HomePage');
		$this->assertTrue($pos !== false);

		$pos = strstr($mail, '+ admin was on projectb');
		$this->assertTrue($pos !== false);

		// Open homepage on the wiki.
		// Watch HomePage and edit HomePage after.
		$this->open(ROOT."/wiki/g/projectb/HomePage");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=User Preferences");
		$this->type("pref[notifyPages]", "");
		$this->clickAndWait("//input[@value='Update Preferences']");
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Watch Page");
		$this->clickAndWait("add");
		$this->clickAndWait("link=HomePage");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "modified by admin");
		$this->type("edit-summary", "testing watchlist");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->clickAndWait("link=User Preferences");

		// Step5: check that an email is received with modification.
		$mail = $this->fetchMail();
		$pos = strstr($mail, '+ modified by admin');
		$this->assertTrue($pos !== false);

		// Check that two different projects have different watchlists
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=User Preferences");
		$this->type("pref[notifyPages]", "ZZTop");
		$this->clickAndWait("//input[@value='Update Preferences']");

		// Now that two projects with wiki are created, try setting the notify pref
		// on projectA and check that there is no impact on the prefs of projectB
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectB");
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=User Preferences");
		$this->assertFalse($this->isTextPresent("ZZTop"));
		$this->type("pref[notifyPages]", "XXXXX");
		$this->clickAndWait("//input[@value='Update Preferences']");

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=User Preferences");
		$this->assertTextPresent("ZZTop");
		$this->assertFalse($this->isTextPresent("XXXXX"));

		// Step 25: try to WatchPage without being logged in.
		// 1) You should have a message "You must sign in to watch pages."
		// 2) You should not have a login banner (no "UserId", no "Password")

		$this->clickAndWait("link=Log Out");
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Watch Page");

		$this->assertTextPresent("You must sign in");
		$this->assertFalse($this->isTextPresent("UserId"));
		$this->assertFalse($this->isTextPresent("Password"));
	}
}
?>
