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

class ActivateWebSvn extends FForge_SeleniumTestCase
{
	function testInitial()
	{
		$this->initSvn();

		$this->createUser('upublic', 110);

		// TEST1: Set SCM access to public & anonymous access
		$this->open(ROOT);
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[scmanon][0]", "label=Allow Anonymous Access");
		$this->clickAndWait("submit");

		// Create simple structure for svn.
		$svn = "svn --non-interactive --no-auth-cache";
		system("$svn --username admin --password myadmin -m 'struct' mkdir ".URL."svn/projecta/branches >/dev/null");
		system("$svn --username admin --password myadmin -m 'struct' mkdir ".URL."svn/projecta/tags >/dev/null");
		system("$svn --username admin --password myadmin -m 'struct' mkdir ".URL."svn/projecta/trunk >/dev/null");

		// Check that anonymous has access to the svn.
		$this->logout();
		$this->open("/projects/projecta/");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("branches/");
		$this->assertTextPresent("tags/");
		$this->assertTextPresent("trunk/");
		$this->assertTextPresent("struct");

		// TEST2: Set SCM access to public & no anonymous.
		$this->login('admin');
		$this->open("/projects/projecta/");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[scmanon][0]", "label=No Anonymous Access");
		$this->clickAndWait("submit");

		// Check that anonymous as NO access to the svn.
		$this->logout();
		$this->open("/projects/projecta/");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->assertTextPresent("Anonymous access not allowed.");

		// Direct acces is redirected to the login page.
		$this->open("/plugins/websvn/listing.php?repname=projecta&path=%2F");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Please enter your Corporate Short Login and Password");

		// Check that a public user has access (logged but not member).
		$this->login('upublic');
		$this->open("/projects/projecta/");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("branches/");
		$this->assertTextPresent("tags/");
		$this->assertTextPresent("trunk/");
		$this->assertTextPresent("struct");

		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[scmpublic][0]", "label=Private");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[scm][0]", "label=No Access");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("Permission denied.");
		$this->assertTextPresent("This project's administrator will have to grant you permission to view this page.");

		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[scm][0]", "label=Read");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		$this->switchUser("uuser");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("Last modification");

		$this->switchUser("uadmin");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->select("//div[@id='maindiv']/table/tbody/tr/td[1]/table/tbody/tr[7]/td/form/select", "label=User");
		$this->clickAndWait("//input[@name='edit' and @value='Edit Role']");
		$this->select("data[scm][0]", "label=Write");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");

		$this->switchUser("uuser");

		system("$svn --username uuser --password password -m 'Create directory' mkdir ".URL."svn/projecta/trunk/mydir >/dev/null");

		system("rm -fr /tmp/svn/");
		system("$svn --username uuser --password password co ".URL."svn/projecta/trunk/ /tmp/svn/ >/dev/null");
		system("touch /tmp/svn/mydir/myfile >/dev/null");
		system("$svn --username uuser --password password add /tmp/svn/mydir/myfile >/dev/null");
		system("$svn ci --username uuser --password password -m 'Create file' /tmp/svn >/dev/null");

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("Last modification");
		$this->assertTextPresent("Create file");

		system("echo 'First line' > /tmp/svn/mydir/myfile");
		system("echo 'Second line' >> /tmp/svn/mydir/myfile");
		system("echo 'Third line' >> /tmp/svn/mydir/myfile");
		system("$svn ci --username uuser --password password -m 'Added three lines' /tmp/svn >/dev/null");
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("Last modification");
		$this->assertTextPresent("Added three lines");

		system("echo 'First line' > /tmp/svn/mydir/myfile");
		system("echo 'Third line changed' >> /tmp/svn/mydir/myfile");
		system("$svn ci --username uuser --password password -m 'Delete second line, modify last line' /tmp/svn >/dev/null");
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Source Code");
		$this->clickAndWait("link=Browse Subversion Repository");
		$this->assertTextPresent("Last modification");
		$this->assertTextPresent("Delete second line, modify last line");

		$this->clickAndWait("link=Compare with Previous");
		$this->assertTextPresent("Compare Revisions");
		$this->assertTextPresent("Third line changed");

		$this->assertText("//td[@class='diffdeleted']", "Second line");
		$this->assertText("//td[@class='diffadded']", "Third line changed");

		// Turn off SCM for the project
		$this->switchUser('uadmin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");

		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Tools");
		$this->click("use_scm");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Project information updated");
		$this->clickAndWait("link=Project Summary");
		$this->assertFalse($this->isTextPresent("Source Code"));
		$this->open("/plugins/websvn/filedetails.php?repname=projecta&path=%2Fmytext.txt");
		$this->assertTextPresent("Error This Project Has Turned Off SCM");
	}
}
?>
