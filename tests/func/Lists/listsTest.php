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

class Lists extends FForge_SeleniumTestCase
{
	/*
	 * This test creates a project with a mailing list.
	 */
	function testNormalLists()
	{
		$this->init();
		$this->clickAndWait("link=Mailing Lists");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add Mailing List");
		$this->type("list_name", "mylist");
		$this->clickAndWait("submit");

		$this->cron("cronjobs/mail/mailing_lists_create.php");

		$this->clickAndWait("link=Mailing Lists");

		$this->assertTextPresent("projecta-mylist@".SITE);
		$this->assertEquals("projecta-mylist@".SITE, $this->getText("link=projecta-mylist@".SITE));
	}

	/*
	 * This test creates a project with external mailing lists.
	 */
	function testlistsTPC()
	{
		$this->init();
		$this->clickAndWait("link=Mailing Lists");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add Mailing List");
		$this->type("list_name", "internal");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Add Mailing List");
		$this->type("list_name", "external");
		$this->click("is_external");
		$this->clickAndWait("submit");

		$this->cron("cronjobs/mail/mailing_lists_create.php");

		$this->clickAndWait("link=Mailing Lists");
		$this->clickAndWait("link=projecta-external Archives");
		$this->assertTextPresent("This mailing list is accessible to Internet users.");
		$this->clickAndWait("link=Mailing Lists");
		$this->clickAndWait("link=projecta-internal Archives");
		$this->assertFalse($this->isTextPresent("This mailing list is accessible to Internet users."));

		$this->cron("cronjobs/mail/generate_external_emails.php");

		$val = file_get_contents("/etc/gforge/http/external_emails.txt");
		$this->assertEquals("projecta-external\n", $val);
	}

	/*
	 * Verify that it is imposible to use name already used by a mailing list
	 */
	function testEmailAddressNotAlreadyUsed() {
		$this->init();
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add forum");
		$this->type("forum_name", "tutu");
		$this->type("description", "Tutu forum");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Forum created successfully");
		$this->clickAndWait("link=Mailing Lists");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add Mailing List");
		$this->type("list_name", "tutu");
		$this->type("description", "Tutu mailing list");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Error: a forum with the same email address already exists");
	}
}
?>
