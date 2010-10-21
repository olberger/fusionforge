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

class ForumsTPC extends FForge_SeleniumTestCase
{
	function testTPCCreate()
	{
		/*
		 * Create a forum, create a message inside.
		 * Add answers and attachements.
		 * Forbid access to tpc users.
		 */
		$this->init();
		$this->createUser('utpc', 110);

		$this->db("UPDATE users SET is_external=1 WHERE user_name='utpc'");

		// Set the external flag on the projectA project.
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Display Full Project List/Edit Projects");
		$this->clickAndWait("link=ProjectA");
		$this->select("form_extern", "label=Yes");
		$this->clickAndWait("submit");

		// Add an external role named 'tpc' to projectA
		$this->clickAndWait("link=[Project Admin]");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("link=Add External Role");
		$this->type("role_name", "tpc");
		$this->select("data[forum][2]", "label=Read");
		$this->clickAndWait("submit");

		// Add utpc user inside the tpc role.
		$this->type("form_unix_name", "utpc");
		$this->select("role_id", "label=tpc");
		$this->clickAndWait("adduser");

		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=help");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "message#1 - tpc ok");
		$this->type("body", "Message#1, access by tpc users");
		$this->clickAndWait("submit");

		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "message#2 - tpc no access");
		$this->type("body", "Message#2, no access by tpc users");
		$this->clickAndWait("submit");

		// Add an attachement with a fake secret file to message#2.
		$this->db("INSERT INTO forum_attachment (attachmentid, userid, dateline, filename, filedata, visible, counter, ".
			"filesize, msg_id, filehash, mimetype) VALUES(1, 102, 1274302119, 'secret.txt','QSBTZWNyZXRGaWxlVGhlcmUgICEhISENCg==',".
			"1, 0, 12, 5, 'XXXX', 'text/plain')");

		// Access list of Messages.
		// Check presence of warning banner
		$this->open("/forum/forum.php?forum_id=2&group_id=6");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("message#1 - tpc ok");
		$this->assertTextPresent("This project is shared with third party users.");

		// A TPC user is trying to access data in a forum (not allowed)
		$this->switchUser('utpc');

		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Forums");
		$this->assertFalse($this->isTextPresent("open-discussion"));
		$this->assertTextPresent("help");
		$this->assertFalse($this->isTextPresent("developers"));

		// Access to open-discussion forum is denied.
		$this->open("/forum/forum.php?forum_id=1&group_id=6");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Permission denied.");
		$this->assertFalse($this->isTextPresent("This project is shared with third party users."));

		// Access to open-discussion forum is denied (short URL).
		$this->open("/forum/forum.php?forum_id=1");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Permission denied.");
		$this->assertFalse($this->isTextPresent("This project is shared with third party users."));

		// Access to developers forum is denied.
		$this->open("/forum/forum.php?forum_id=3&group_id=6");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Permission denied.");
		$this->assertFalse($this->isTextPresent("This project is shared with third party users."));

		// Direct access in to message#2 => not allowed.
		$this->open("/forum/forum.php?thread_id=4&forum_id=1&group_id=6");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Permission denied.");
		$this->assertFalse($this->isTextPresent("message#2 - tpc no access"));

		// No access on an attached file.
		$this->open("/forum/attachment.php?attachid=1&group_id=6&forum_id=1");
		$this->waitForPageToLoad("30000");
		$this->assertFalse($this->isTextPresent("A SecretFile"));
		$this->assertTextPresent("Permission denied.");

//		// No access on the admin part of the tracker.
//		$this->open("/tracker/admin/?group_id=6&atid=101");
//		$this->waitForPageToLoad("30000");
//		$this->assertFalse($this->isTextPresent("Manage Custom Fields"));
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the admin part of the tracker.
//		$this->open("/tracker/admin/?group_id=6");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the reporting page.
//		$this->open("/tracker/reporting/?group_id=6&atid=101");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//		
//		// No access on the tracker activity image
//		$this->open("/tracker/reporting/trackeract_graph.php?SPAN=3&start=1251763200&end=1257033600&group_id=6&atid=101");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the tracker activity pie graph
//		$this->open("/tracker/reporting/trackerpie_graph.php?SPAN=3&start=1251763200&end=1257033600&group_id=6&atid=101&area=assignee");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//
//		// Tests on another project (group_id=7)
//		// No access the trackers in another project (not member of).
//		$this->open("/tracker/?group_id=7");
//		$this->waitForPageToLoad("30000");
//		$this->assertFalse($this->isTextPresent("Tech Support Tracking System"));
//		$this->assertTextPresent("Permission denied.");
//
//		// No access the bugs tracker in another project (not member of).
//		$this->open("/tracker/?atid=105&group_id=7&func=browse");
//		$this->waitForPageToLoad("30000");
//		$this->assertFalse($this->isTextPresent("No items found"));
//		$this->assertTextPresent("Permission denied.");
//
//		// No access to a bug in another project (not member of).
//		$this->open("/tracker/?func=detail&aid=1&group_id=7&atid=105");
//		$this->waitForPageToLoad("30000");
//		$this->assertFalse($this->isTextPresent("Summary of bug#1"));
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the admin part of the tracker in another project.
//		$this->open("/tracker/admin/?group_id=7&atid=105");
//		$this->waitForPageToLoad("30000");
//		$this->assertFalse($this->isTextPresent("Manage Custom Fields"));
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the admin part of the tracker.
//		$this->open("/tracker/admin/?group_id=7");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the reporting page.
//		$this->open("/tracker/reporting/?group_id=7&atid=105");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//		
//		// No access on the tracker activity image
//		$this->open("/tracker/reporting/trackeract_graph.php?SPAN=3&start=1251763200&end=1257033600&group_id=7&atid=105");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");
//
//		// No access on the tracker activity pie graph
//		$this->open("/tracker/reporting/trackerpie_graph.php?SPAN=3&start=1251763200&end=1257033600&group_id=7&atid=105&area=assignee");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("Permission denied.");

	}
}
?>
