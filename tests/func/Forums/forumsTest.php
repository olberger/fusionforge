<?php
/*
 * Copyright (C) 2008 Alain Peyrat <aljeux@free.fr>
 * Copyright (C) 2009 Alain Peyrat, Alcatel-Lucent
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

class CreateForum extends FForge_SeleniumTestCase
{
	function testSimplePost()
	{
		// Create the first message (Message1/Text1).
		$this->init();
		$this->clickAndWait("link=Forums");
		$this->assertFalse($this->isTextPresent("Permission denied."));
		$this->assertTextPresent("open-discussion");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "Message1");
		$this->type("body", "Text1");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->clickAndWait("link=Forums");
		$this->assertTextPresent("open-discussion");
		$this->clickAndWait("link=open-discussion");
		$this->assertTextPresent("Message1");

		// Create a message with funny characters
		$this->clickAndWait("link=Forums");
		$this->assertFalse($this->isTextPresent("Permission denied."));
		$this->assertTextPresent("open-discussion");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "subject: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->type("body", "body: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->clickAndWait("link=Forums");
		$this->assertTextPresent("open-discussion");
		$this->clickAndWait("link=open-discussion");
		$this->assertTextPresent("subject: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->click("link=subject: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("body: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
	}

	/*
	 * Simulate a click on the link from a mail.
	 * As the forum is private, the users should be
	 * redirected to the login prompt saying that he has
	 * to login to get access to the message. Once logged,
	 * he should be redirected to the given forum.
	 */
	function testSimpleAccessWhenPrivate()
	{
		$this->init();
		$this->logout();

		$this->open( ROOT.'/forum/message.php?msg_id=3' );
		$this->waitForPageToLoad("30000");
		$this->type("form_loginname", 'admin');
		$this->type("form_pw", 'myadmin');
		$this->clickAndWait("login");
		$this->assertTextPresent("Welcome to Developers");
	}

	/*
	 * Simulate a user non logged that will reply
	 * to a message in a forum. He will be redirected
	 * to the login page, then will reply and then
	 * we check that his reply is present in the thread.
	 */
	function testReplyToMessage()
	{
		$this->init();
		$this->logout();

		$this->open("/projects/projecta/");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Welcome to Open-Discussion");
		$this->clickAndWait("link=[ reply ]");
		$this->assertTextPresent("Cookies must be enabled past this point.");
//		$this->assertEquals("ACOS Forge - Login", $this->getTitle());
		$this->type("form_loginname", "admin");
		$this->type("form_pw", 'myadmin');
		$this->clickAndWait("login");
		$this->type("body", "Here is my 19823 reply");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->clickAndWait("//td[@id='main']/table[1]/tbody/tr/td[1]/a/strong");
		$this->assertTextPresent("Here is my 19823 reply");

	}
	
	/*
	 * Verify that it is imposible to use name already used by a mailing list
	 */
	function testEmailAddressNotAlreadyUsed() {
		$this->init();
		$this->clickAndWait("link=Mailing Lists");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add Mailing List");
		$this->type("list_name", "toto");
		$this->type("description", "Toto mailing list");
		$this->clickAndWait("submit");
		$this->assertTextPresent("List Added");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add forum");
		$this->type("forum_name", "toto");
		$this->type("description", "Toto forum");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Error: a mailing list with the same email address already exists");
	}
	
	function testHtmlFiltering()
	{
		// Create the first message (Message1/Text1).
		$this->init();
		$this->clickAndWait("link=Forums");
		$this->assertFalse($this->isTextPresent("Permission denied."));
		$this->assertTextPresent("open-discussion");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", "Message1");
		$this->type("body", "Text1 <script>Hacker inside</script> done");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		$this->clickAndWait("link=Forums");
		$this->assertTextPresent("open-discussion");
		$this->clickAndWait("link=open-discussion");
		$this->assertTextPresent("Message1");
		$this->assertFalse($this->isTextPresent("Hacker inside"));
		$this->assertFalse($this->isTextPresent("Text1  done"));
	}
}
?>
