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

class SvnCommitEmail extends FForge_SeleniumTestCase
{
	function testSvnCommitEmail()
	{
		/*
		 * Create a projectA, 
		 * Activate SCM & commit plugin.
		 * Make a commit
		 * Verify that commit email has been correctly send/stored.
		 */
		$this->initSvn();

	    $this->clickAndWait("link=Project Admin");
	    $this->clickAndWait("link=Tools");
	    $this->click("use_svncommitemail");
	    $this->clickAndWait("submit");
	    
	    $this->clearMail();
		
		// Run the svn create to get the repository with the hooks.
		$this->cron("plugins/websvn/cronjobs/create_svn.php");

		$svn = "svn --non-interactive --no-auth-cache";
		$url = URL."svn/projecta/";

		// Try accessing the svn repository using admin rights
		// => Should be allowed.
		system("rm -fr /tmp/svn.test");
		mkdir("/tmp/svn.test");

		system("cd /tmp/svn.test; $svn --username admin --password myadmin co $url >/dev/null", $ret);
		$this->assertEquals($ret, 0);

		system("echo 'this is a simple text' > /tmp/svn.test/projecta/mytext.txt");
		
		system("cd /tmp/svn.test/projecta; $svn --username admin --password myadmin add mytext.txt >/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		system("cd /tmp/svn.test/projecta; $svn --username admin --password myadmin ci -m 'added mytext' >/dev/null", $ret);
		$this->assertEquals($ret, 0);
		
		$this->assertContains('Added: mytext.txt', $this->fetchMail());
		
		// Now, test same kind of operation with an update.
		system("echo 'Another line' >> /tmp/svn.test/projecta/mytext.txt");
		system("cd /tmp/svn.test/projecta; $svn --username admin --password myadmin ci -m 'updated mytext' >/dev/null", $ret);

		$this->assertContains('Modified: mytext.txt', $this->fetchMail());

		system("rm -fr /tmp/svn.test");
	}
}
?>
