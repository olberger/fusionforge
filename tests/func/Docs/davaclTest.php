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

class DavAcl extends FForge_SeleniumTestCase
{
	function testDavAcl()
	{
		/*
		 * Create a projectA with 6 users.
		 * uadmin 	has the Admin role on the project.
		 * usupport has the Community Support Assistant role on the project
		 * ucontrib has the Contributor role on the project
		 * ucoredev has the Core Developer role on the project
		 * uuser	has the User role on the project
		 * unoaccess has the No Access role on the project
		 * 
		 * And a user unonmember which is not member of the project for testing
		 * public access to the ressource.
		 */
		system("rm -fr /opt/groups/projecta");

		$this->init();
		
		// Run the svn create to get the repository with the hooks.
		$this->cron("cronjobs/dav-svn/create_groups.php /opt");
		
		$this->open(ROOT);
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->type("form_unix_name", "uadmin");
		$this->select("role_id", "label=Admin");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "usupport");
		$this->select("role_id", "label=Community Support Assistant");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "ucontrib");
		$this->select("role_id", "label=Contributor");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "ucoredev");
		$this->select("role_id", "label=Core Developer");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "uuser");
		$this->select("role_id", "label=User");
		$this->clickAndWait("adduser");

		// Create a new role 'No Access' with no access.
		// And put the new user 'noaccess' in this role.
		$this->clickAndWait("link=Add Role");
		$this->type("role_name", "No Access");
		$this->select("data[dav][0]", "label=No Access");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->type("form_unix_name", "unoaccess");
		$this->select("role_id", "label=No Access");
		$this->clickAndWait("adduser");

		$cmd = "wget --proxy=off -O /dev/null";
		$url = URL.'groups/projecta/';
		
		// Try accessing the webdav directory using admin rights
		// => Should be allowed.
		system("$cmd --user=admin --password=myadmin $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);

		// Try accessing the webdav directory using admin rights (wrong password)
		// => Not allowed.
		system("$cmd --user=admin --password=myAdmin $url &>/dev/null", $ret);
		$this->assertEquals($ret, 1);

		// Try accessing the webdav directory using admin rights.
		// => Should be allowed.
		system("$cmd --user=usupport --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		// Try accessing the webdav directory using admin rights.
		// => Should be allowed
		system("$cmd --user=ucontrib --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		// Try accessing the webdav directory using admin rights.
		// => Should be allowed
		system("$cmd --user=ucoredev --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		// Try accessing the webdav directory using admin rights.
		// => Should be allowed
		system("$cmd --user=uuser --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		// Try accessing the webdav directory using No Access rights.
		// But as the dav is public, acces is allowed (not very nice).
		// => Not allowed (still access).
		system("$cmd --user=unoaccess --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		// Try accessing the webdav directory using Observer rights.
		// => Not allowed (no anonymous access).
		system("$cmd $url &>/dev/null", $ret);
		$this->assertEquals($ret, 1);

		// Try accessing the webdav directory using public rights.
		// => Allowed (repository is public).
		system("$cmd --user=unonmember --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		// Change Observer from public => private.
	    $this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
	    $this->select("data[davpublic][0]", "label=Private");
	    $this->clickAndWait("submit");

		// Try accessing the webdav directory using Observer rights.
		// => Not allowed (no anonymous access).
		system("$cmd $url &>/dev/null", $ret);
		$this->assertEquals($ret, 1);

		// Try accessing the webdav directory using public rights.
		// => Not allowed (repository is private).
		system("$cmd --user=unonmember --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 1);

		// Change Observer from no anon => anon allowed (but still private).
		// Expected is still no access for observers.
	    $this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
	    $this->clickAndWait("edit");
	    $this->select("data[davanon][0]", "label=Allow Anonymous Access");
	    $this->clickAndWait("submit");

		// Try accessing the webdav directory using Observer rights.
		// => Not allowed (anonymous access but private => no access).
		system("$cmd $url &>/dev/null", $ret);
		$this->assertEquals($ret, 1);

		// Try accessing the webdav directory using Observer rights.
		// => Not allowed (repository is private).
		system("$cmd --user=unonmember --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 1);

		// Change Observer to public & anon.
		// Expected is still access for observers.
	    $this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
	    $this->clickAndWait("edit");
	    $this->select("data[davpublic][0]", "label=Public");
	    $this->select("data[davanon][0]", "label=Allow Anonymous Access");
	    $this->clickAndWait("submit");

		// Try accessing the webdav directory using Observer rights.
		// => Not allowed (anonymous access but private => no access).
		system("$cmd $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);

		// Try accessing the webdav directory using Observer rights.
		// => Not allowed (repository is private).
		system("$cmd --user=unonmember --password=password $url &>/dev/null", $ret);
		$this->assertEquals($ret, 0);
	}
}
?>
