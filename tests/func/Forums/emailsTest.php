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

class CreateEmails extends FForge_SeleniumTestCase
{
	/*
	 * Tests are:
	 * 1) Reply to a forum by email (using plain text message).
	 * 2) Reply to a forum by email (using an HTML message)
	 * 2) Post a new message by email (using plain text message).
	 */
	function testReplyByEmail()
	{
		$dir = dirname(__FILE__);
		$php = "/usr/bin/php";
		$forum = dirname(dirname(dirname($dir)))."/src/cronjobs/forum_gateway.php";

		// Create the first message (Message1/Text1).
		$this->init();
		$this->clickAndWait("link=Forums");
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

		// Send a reply to Message1 by email (text plain).
		system("$php $forum projecta open-discussion < $dir/mail1.txt");
		$this->clickAndWait("link=Message1");
		$this->assertTextPresent("Answer to message1");

		// Send a reply to Message1 by email (html post with outlook).
		system("$php $forum projecta open-discussion < $dir/mail2.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Message1");
		$this->assertTextPresent("Message1 with text and html post");

		// Send an initial mail message to a forum (text/plain).
		system("$php $forum projecta open-discussion < $dir/mail_new_txt_message.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=New txt message (200720101152)");
		$this->assertTextPresent("This is a new txt message (200720101152)");

		// Test text/plain, utf-8 => format accepted
		system("$php $forum projecta open-discussion < $dir/mail_all_txt_utf8.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=New txt message (200720101152)");
		$this->assertTextPresent('All txt UTF-8 ²&é~"#\'{([-|è`_\ç^à@)°]=+}€^ê¨ë$£¤ù%*µ<>,?;.:/!§');

		// Send an initial mail message to a forum (multipart text/plain and text/html, base64 encoded, utf-8).
		system("$php $forum projecta open-discussion < $dir/mail_new_html_message.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=New HTML message (200720101148)");
		$this->assertTextPresent("This is a new HTML message (200720101148)");

		// Test multipart text/plain and text/html, base64 encoded, utf-8 => format accepted
		system("$php $forum projecta open-discussion < $dir/mail_all_html_utf8.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=New HTML message (200720101148)");
		$this->assertTextPresent('All HTML UTF-8 ²&é~"#\'{([-|è`_\ç^à@)°]=+}€^ê¨ë$£¤ù%*µ<>,?;.:/!§');

		// Test iso-8859-15 => format accepted
		system("$php $forum projecta open-discussion < $dir/mail_iso-8859-15.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->assertTextPresent('Et re-voilà le père noël');

		// Test text/plain iso-8859-1 (latin 1) => format accepted
		system("$php $forum projecta open-discussion < $dir/mail_txt_iso-8859-1.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->click('link=All txt iso-8859-1 (210720101244)');
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent('²&é~"#\'{([-|è`_\ç^à@)°]=+}^ê¨ë$£¤ù%*µ<>,?;.:/!§');

		// Test multipart text/plain and text/html, quoted-printable encoded, iso-8859-1 (latin 1) => format accepted
		system("$php $forum projecta open-discussion < $dir/mail_html_iso-8859-1.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->click('link=All HTML iso-8859-1 (210720101544)');
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent('²&é~"#\'{([-|è`_\ç^à@)°]=+}^ê¨ë$£¤ù%*µ<>,?;.:/!§');

		// Test iso-8859-1 with following string in the subject:
		// =?iso-8859-1?Q?=E8re_no=EBl?=
		// This string can generate a bug because of '?=' after the 'Q'
		system("$php $forum projecta open-discussion < $dir/mail_iso-8859-1_2.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Et re-voilà le père noël");
		$this->assertTextPresent('Voilà le père noël, et toujours en latin 1');

		// Test text/plain Windows-1252 => format accepted
		system("$php $forum projecta open-discussion < $dir/mail_windows-1252.txt");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->click('link=All txt windows-1252 (210720101232)');
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent('²&é~"#\'{([-|è`_\ç^à@)°]=+}€^ê¨ë$£¤ù%*µ<>,?;.:/!§œ');
	}
}
?>
