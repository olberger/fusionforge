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

class WikiACLSimple extends FForge_SeleniumTestCase
{
	function testwikiACLSimple()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		$this->clickAndWait("link=HomePage");

		$this->clickAndWait("link=User Preferences");
		$this->click("pref[setaclMenuItem]");
		$this->clickAndWait("//input[@value='Update Preferences']");

		// TEST01:
		// Give write access to the HomePage
		// to users that are not member of the project.
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Access Rights");

		$this->clickAndWait("admin_setacl[aclliberal]");
		$this->assertTextPresent("ACL changed for page 'HomePage'");
		$this->assertTextPresent("from 'None'");
		$this->assertTextPresent("to 'view:_EVERY; edit:_EVERY; create:_EVERY; list:_EVERY; remove:_ADMIN,_OWNER; purge:_ADMIN,_OWNER; dump:_ADMIN,_OWNER; change:_ADMIN,_OWNER'.");

		// TEST02:
		// Give read access only to project members.
		// Page is: "MySecret"
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "All our secrets are here: [[MySecret]]");
		$this->type("edit-summary", "adding link to MySecret page");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");

		$this->clickAndWait("link=exact:?");
		$this->type("edit-content", "The root password is : warzone2100");
		$this->type("edit-summary", "adding root password");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");

		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=MySecret");
		$this->clickAndWait("link=Access Rights");

		$this->clickAndWait("admin_setacl[aclrestricted]");
		$this->assertTextPresent("ACL changed for page 'MySecret'");
		$this->assertTextPresent("from 'None'");
		$this->assertTextPresent("to 'view:_AUTHENTICATED,-_EVERY; edit:_AUTHENTICATED,-_EVERY; create:_AUTHENTICATED,-_EVERY; list:_AUTHENTICATED,-_EVERY; remove:_ADMIN,_OWNER; purge:_ADMIN,_OWNER; dump:_ADMIN,_OWNER; change:_ADMIN,_OWNER'.");

		// Now check that as admin can see the content of the page
		// but unonmember cannot.
		$this->clickAndWait("link=Page");
		$this->assertTextPresent("warzone2100");

		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=MySecret");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Sign In");
		$this->assertTextPresent("Missing PagePermission: view MySecret is disallowed on this wiki for not authenticated user");

		// TEST03:
		// Include the secret page in the HomePage (by unonmember).
		//
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "All our secrets are here: [[MySecret]]\n\n{{MySecret}}");
		$this->clickAndWait("edit[save]");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Illegal inclusion of page MySecret: no read access");

		// TEST04:
		// Edit the page by Administrator, view it (to ensure page is cached).
		// Then try to view as unonmember
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "Updated: Game ? {{MySecret}}");
		$this->type("edit-summary", "game");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("warzone2100");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Illegal inclusion of page MySecret: no read access");

		// TEST05: Test includepage plugin.
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "<<IncludePage page=MySecret>>");
		$this->type("edit-summary", "IncludePage test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("warzone2100");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Illegal inclusion of page MySecret: no read access");

		// TEST06: Test PageDump plugin.
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "<<PageDump page=MySecret>>");
		$this->type("edit-summary", "PageDump test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("warzone2100");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Illegal access to page MySecret: no read access");

		// TEST07: Test IncludePages plugin.
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "<<IncludePages pages=MySecret,MySecret>>");
		$this->type("edit-summary", "IncludePages test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("warzone2100");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Illegal inclusion of page MySecret: no read access");

		// TEST08: Test the diff plugin.
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "[[MySecret]]\n<<Diff pagename=MySecret>>");
		$this->type("edit-summary", "diff test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		
		$this->clickAndWait("link=MySecret");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "!!! The root password is : warzone2100\n\nwow");
		$this->type("edit-summary", "adding wow");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		
		$this->clickAndWait("link=Wiki");
		$this->assertTextPresent("wow");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertFalse($this->isTextPresent("wow"));
		$this->assertTextPresent("Illegal access to page MySecret: no read access");

		// TEST09: Test the unfold plugin.
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "[[MySecret]] and [[MySecret/MyPage]]\n<<UnfoldSubpages pagename=MySecret>>");
		$this->type("edit-summary", "unfoldplugin test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		
		$this->clickAndWait("link=exact:?");
		$this->clickAndWait("link=Create Page");
		$this->type("edit-content", "Adding spore as game.");
		$this->type("edit-summary", "adding spore");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		
		$this->clickAndWait("link=Wiki");
		$this->assertTextPresent("spore");

		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertFalse($this->isTextPresent("wow"));
		$this->assertFalse($this->isTextPresent("spore"));
		$this->assertTextPresent("Illegal inclusion of page MySecret/MyPage: no read access");

		// TEST10: Test the CreateTOC plugin
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "<<CreateToc pagename=MySecret>>");
		$this->type("edit-summary", "CreateTOC test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("warzone2100");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
		$this->assertTextPresent("Illegal access to page MySecret: no read access");

		// TEST11: Test the FullTextSearch plugin.
		$this->switchUser('admin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "<<FullTextSearch s=password>>");
		$this->type("edit-summary", "FullTextSearch test");
		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("warzone2100");
		
		$this->switchUser('unonmember');

		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Wiki");
		$this->assertFalse($this->isTextPresent("warzone2100"));
	}
}
?>
