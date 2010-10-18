<?php
/*
 * Copyright (C) 2008 Alain Peyrat <aljeux@free.fr>
 * Copyright (C) 2009 - 2010 Alain Peyrat, Alcatel-Lucent
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

class CreateTracker extends FForge_SeleniumTestCase
{
	function testSimpleCreate()
	{
		// Test: Create a simple bug report (Message1/Text1).
		$this->init();
		$this->clickAndWait("link=Tracker");
		$this->assertTextPresent("Bugs");
		$this->assertTextPresent("Support");
		$this->assertTextPresent("Patches");
		$this->assertTextPresent("Feature Requests");

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->click("summary");
		$this->type("summary", "This is the summary");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Body Is Required");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("details", "This is the detailed description.");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Summary Is Required");

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->type("details", "L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->click("link=L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");

		// View tracker entry in detail
		$this->assertTextPresent("ProjectA");
		$this->assertTextPresent("Assigned to");
		$this->assertTextPresent("Priority");
		$this->assertTextPresent("Summary");
		$this->assertTextPresent("Detailed description");

		// Test: Adding a comment and checking that it is recorded.
		$this->type("details", 'This is comment 1');
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent('This is comment 1');

		// Test: Adding a second comment and checking that it is recorded.
		$this->type("details", 'Comment 2 \n added');
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent('Comment 2 \n added');
		$this->assertTextPresent("This is comment 1");

		// Test: Adding another comment (chars) and checking that it is recorded.
		$this->type("details", "This & été");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("This & été");

		// Test: Updating the URL extra field and checking that it is recorded.
		$this->type("extra_fields[8]", "http://www.fusionforge.org");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Summary1");
		$this->assertEquals("http://www.fusionforge.org", $this->getValue("extra_fields[8]"));

		// Test: Updating the priority and checking that it is recorded.
		$this->select("priority", "label=5 - Highest");
		$this->clickAndWait("submit");
		$this->assertTextPresent("5");
		$this->clickAndWait("link=Summary1");
	}

	function testFeatureRequest()
	{
		$this->init();
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Feature Requests");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");
	}

	function testSupport()
	{
		$this->init();
		$this->clickAndWait("link=Trackers");
		// There are two links "Support"
		$this->clickAndWait("//td[@id='main']/table/tbody/tr[2]/td[1]/a");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");
	}

	function testPatches()
	{
		$this->init();
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Patches");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("Description1");
	}

	function testCreateNewTracker()
	{
		$this->init();
		$this->switchUser('uadmin');
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=New Tracker");
		$this->type("name", "Meeting minutes");
		$this->type("description", "Meeting minutes");
		$this->click("is_public");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Tracker created successfully");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Meeting minutes");
		$this->clickAndWait("link=Submit New");
		$this->select("assigned_to", "label=uadmin Lastname");
		$this->select("priority", "label=5 - Highest");
		$this->click("//option[@value='5']");
		$this->type("summary", "New Tracker Item");
		$this->click("summary");
		$this->type("details", "New Tracker Detailed description");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");
		$this->assertTrue($this->isElementPresent("link=New Tracker Item"));
	}

	function testNewTrackerEntryNotLogged()
	{
		$this->init();
		$this->logout();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->assertTextPresent("Please enter your Corporate Short Login and Password");
		$this->type("form_loginname", "uuser");
		$this->type("form_pw", "password");
		$this->clickAndWait("login");
		$this->assertTextPresent("Welcome uuser Lastname");
		$this->assertTextPresent("Operating System:");
		$this->type("summary", "This is the summary");
		$this->type("details", "Detailed description");
		$this->clickAndWait("submit");

		$this->switchUser('uadmin');
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->clickAndWait("edit");
		$this->select("data[trackeranon][101]", "label=Allow Anonymous Posts");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Updated Role");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Log Out");
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		// TBD: Check you can post anonymously
	}

	function testUpdateTrackerAssignedToYou()
	{
		$this->init();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->select("assigned_to", "label=ucoredev Lastname");
		$this->type("summary", "Assigned to a tech");
		$this->type("details", "Assigned to a tech");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Assigned to a tech");
		$this->logout();
		$this->clickAndWait("link=Log In");
		$this->type("form_loginname", "ucoredev");
		$this->type("form_pw", "password");
		$this->clickAndWait("login");
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Assigned to a tech");
		$this->select("extra_fields[1]", "label=Macintosh");
		$this->select("extra_fields[3]", "label=MacOS X");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully updated");
		$this->clickAndWait("link=Assigned to a tech");
		$this->assertTextPresent("Macintosh");
		$this->assertTextPresent("MacOS X");
	}

	function testMonitorTracker()
	{
		$this->init();
		// Submit a tracker entry in Bugs
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "My tracker entry");
		$this->click("//td[@id='main']/form/table/tbody/tr[10]/td/strong");
		$this->type("details", "My tracker entry description.");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");

		// Monitor entire tracker
		$this->clickAndWait("link=Monitor");
		$this->assertTextPresent("Now Monitoring Tracker");
		
		// Test mail when creating tracker
		$this->clearMail();
		// Creating tracker
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Test monitor tracker");
		$this->click("//td[@id='main']/form/table/tbody/tr[10]/td/strong");
		$this->type("details", "Body of test monitor tracker");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");
		// Verify mail
		$mailRef = file_get_contents(dirname(__FILE__).'/mail1.txt');
		$mailRef = str_replace('{site}', SITE, $mailRef);
		$mail = $this->getMail();
		$mail = preg_replace('/\d\d\d\d-\d\d-\d\d \d\d:\d\d/', '{date}', $mail);
		$this->assertEquals($mailRef, $mail);

		// Stop monitoring entire tracker
		$this->clickAndWait("link=Stop Monitor");
		$this->assertTextPresent("Tracker Monitoring Deactivated");

		// Monitor tracker entry
		$this->clickAndWait("link=My tracker entry");
		$this->assertTextPresent("[#1] My tracker entry");
		$this->clickAndWait("//td[@id='main']/form/table[1]/tbody/tr/td[1]/a/strong");
		$this->assertTextPresent("Now Monitoring Artifact");

		// Stop monitoring tracker entry
		$this->clickAndWait("link=My tracker entry");
		$this->clickAndWait("//td[@id='main']/form/table[1]/tbody/tr/td[1]/a/strong");
		$this->assertTextPresent("Artifact Monitoring Deactivated");

		// Monitoring an entire tracker while not being logged.
		$this->logout();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=My tracker entry");
		$this->clickAndWait("link=Monitor");
		$this->assertTextPresent("Please enter your Corporate Short Login and Password");
		$this->type("form_loginname", "uuser");
		$this->type("form_pw", "password");
		$this->clickAndWait("login");
		$this->assertTextPresent("Now Monitoring Tracker");
	}

	function testHtmlInDetails()
	{
		// Test: Create a simple bug report (Message1/Text1).
		$this->init();
		$this->clickAndWait("link=Tracker");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1 <bold>");
		$this->type("details", "0x426582a1 in kill () at <stdin>");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1 <bold>");
		$this->clickAndWait("link=Summary1 <bold>");
		$this->assertTextPresent("");
		$this->assertTextPresent("0x426582a1 in kill () at <stdin>");

		$this->switchUser('ucontrib');

		// Test: Adding a comment and checking that it is recorded.
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Tracker");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1 <bold>");
		$this->type("details", "This is comment 1");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Summary1 <bold>");
		$this->assertFalse($this->isTextPresent("0x426582a1 in kill () at &lt;stdin&gt;"));
	}

	function testExtraFields()
	{
		// Testing extra-fields
		$this->init();
		$this->clickAndWait("link=Tracker");
		$this->clickAndWait("link=Bugs");
		$this->click("//a[contains(@href, '".ROOT. "/tracker/admin/?group_id=6&atid=101')]");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Manage Custom Fields");
		$this->type("name", "Number");
		$this->type("alias", "number");
		$this->click("field_type");
		$this->clickAndWait("post_changes");
		$this->click("//a[contains(@href, '".ROOT. "/tracker/admin/index.php?add_opt=1&boxid=22&group_id=6&atid=101')]");
		$this->waitForPageToLoad("30000");
		$this->type("name", "1");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");
		$this->type("name", "2");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");

		// Testing [#3609]: Select Box does not accept 0 as choice
		$this->type("name", "0");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element inserted");

		// Testing [#3649]: 0 not accepted when modifying a select list value
		$this->open(ROOT."/tracker/admin/index.php?group_id=6&atid=101&add_extrafield=1");
		$this->clickAndWait("link=0 [Edit]");
		$this->type("name", "10");
		$this->clickAndWait("post_changes");
		$this->clickAndWait("link=10 [Edit]");
		$this->type("name", "0");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element updated");

		// Test that an element can be updated without any changes.
		$this->clickAndWait("link=0 [Edit]");
		$this->type("name", "0");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element updated");

		// Test that an element can not be changed to an already existing one.
		$this->clickAndWait("link=0 [Edit]");
		$this->type("name", "2");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Element name already exists");
	}

	function testCustomFields() {
		$this->init();
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Manage Custom Fields");

		// Create a select box
		$this->type("name", "SelectBox");
		$this->type("alias", "selectbox");
		$this->click("field_type");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("SelectBox");

		// Create a radio button
		$this->type("name", "RadioButton");
		$this->type("alias", "radiobutton");
		$this->click("//input[@name='field_type' and @value='3']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("RadioButton");

		// Create a text field
		$this->type("name", "TextField");
		$this->type("alias", "textfield");
		$this->click("//input[@name='field_type' and @value='4']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("TextField");

		// Create a text area
		$this->type("name", "TextArea");
		$this->type("alias", "textarea");
		$this->click("//input[@name='field_type' and @value='6']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("TextArea");

		// Create a custom status
		$this->type("name", "CustomStatus");
		$this->type("alias", "customstatus");
		$this->click("//input[@name='field_type' and @value='7']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("CustomStatus");

		// Create an integer
		$this->type("name", "AnInteger");
		$this->type("alias", "aninteger");
		$this->click("//input[@name='field_type' and @value='10']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("AnInteger");

		// Verify if custom fields are present when a new tracker is created
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->assertTextPresent("SelectBox");
		$this->assertTextPresent("RadioButton");
		$this->assertTextPresent("TextField");
		$this->assertTextPresent("TextArea");
		$this->assertTextPresent("CustomStatus");
		$this->assertTextPresent("AnInteger");
		$this->select("extra_fields[3]", "label=MacOS X");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->click("//option[@value='26']");
		$this->select("extra_fields[5]", "label=v1.1");
		$this->select("extra_fields[7]", "label=Accepted As Bug");
		$this->click("extra_fields[23]");
		$this->type("extra_fields[24]", "My Text Field");
		$this->type("extra_fields[25]", "My Text Area");
		$this->click("extra_fields[6]");
		$this->select("extra_fields[6]", "label=blocker");
		$this->select("extra_fields[3]", "label=MacOS X");
		$this->select("extra_fields[1]", "label=Macintosh");
		$this->select("extra_fields[4]", "label=Cog A");
		$this->select("extra_fields[2]", "label=Software A");
		$this->select("assigned_to", "label=ucoredev Lastname");
		$this->click("//option[@value='106']");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");
		$this->clickAndWait("link=Summary1");

		$this->assertEquals("My Text Field", $this->getValue("extra_fields[24]"));
		$this->assertEquals("My Text Area", $this->getText("extra_fields[25]"));

		// Verify if order by custom field is possible in advanced query
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		//$this->click("link=Advanced queries");
		$this->clickAndWait("link=Build Query");
		$this->assertTextPresent("AnInteger Assignee Close Date Component CustomStatus Hardware ID Open Date Operating System Priority Product RadioButton Resolution SelectBox Severity Submitter Summary TextArea TextField URL Version");

		// Modify custom fields and check modifications are logged

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1");
		$this->select("extra_fields[3]", "label=Linux");
		$this->click("//option[@value='27']");
		$this->type("extra_fields[24]", "Champ texte");
		$this->type("extra_fields[25]", "Zone de texte.");
		$this->click("extra_fields[6]");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully updated");
		$this->clickAndWait("link=Summary1");
		$this->assertEquals("Champ texte", $this->getValue("extra_fields[24]"));
		$this->assertEquals("Zone de texte.", $this->getText("extra_fields[25]"));
		// Old values
		$this->assertTextPresent("My Text Field");
		$this->assertTextPresent("My Text Area");
	}

	function testMassUpdate() {
		$this->init();
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Manage Custom Fields");

		// Create a select box
		$this->type("name", "SelectBox");
		$this->type("alias", "selectbox");
		$this->click("field_type");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("SelectBox");

		// Create a radio button
		$this->type("name", "RadioButton");
		$this->type("alias", "radiobutton");
		$this->click("//input[@name='field_type' and @value='3']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("RadioButton");

		// Create a text field
		$this->type("name", "TextField");
		$this->type("alias", "textfield");
		$this->click("//input[@name='field_type' and @value='4']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("TextField");

		// Create a text area
		$this->type("name", "TextArea");
		$this->type("alias", "textarea");
		$this->click("//input[@name='field_type' and @value='6']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("TextArea");

		// Create a custom status
		$this->type("name", "CustomStatus");
		$this->type("alias", "customstatus");
		$this->click("//input[@name='field_type' and @value='7']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("CustomStatus");

		// Create an integer
		$this->type("name", "AnInteger");
		$this->type("alias", "aninteger");
		$this->click("//input[@name='field_type' and @value='10']");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Extra field inserted");
		$this->assertTextPresent("AnInteger");

		// Create 10 tracker elements
		for ($i=1; $i<=10; $i++) {
			$this->clickAndWait("link=Trackers");
			$this->clickAndWait("link=Bugs");
			$this->clickAndWait("link=Submit New");
			$this->type("summary", "Summary$i");
			$this->type("details", "Description$i");
			$this->click("//option[@value='26']");
			$this->select("extra_fields[5]", "label=v1.1");
			$this->select("extra_fields[7]", "label=Accepted As Bug");
			$this->click("extra_fields[23]");
			$this->type("extra_fields[24]", "My Text Field");
			$this->type("extra_fields[25]", "My Text Area");
			$this->click("extra_fields[6]");
			$this->select("extra_fields[6]", "label=blocker");
			$this->select("extra_fields[3]", "label=MacOS X");
			$this->select("extra_fields[1]", "label=Macintosh");
			$this->select("extra_fields[4]", "label=Cog A");
			$this->select("extra_fields[2]", "label=Software A");
			$this->select("assigned_to", "label=ucoredev Lastname");
			$this->click("//option[@value='106']");
			$this->clickAndWait("submit");
			$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");
		}

		// Do a mass update

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->open("/tracker/?func=browse&group_id=6&atid=101");
		$this->click("link=Check  all");
		$this->select("extra_fields[3]", "label=Linux");
		$this->clickAndWait("//input[@name='submit' and @value='Mass update']");
		$this->assertTextPresent("Updated Successfully");

		// Logout so that value is displayed as text, not menu
		$this->logout();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		// Check mass update has been done
		for ($i=1; $i<=10; $i++) {
			$this->clickAndWait("link=Trackers");
			$this->clickAndWait("link=Bugs");
			$this->clickAndWait("link=Summary$i");

			// New value
			$this->assertTextPresent("Linux");
			// Old value
			$this->assertTextPresent("MacOS X");
		}
	}

	/*
	 * Test the "Build Task Relation" by creating a new task from
	 * an artifact.
	 */
	function testCreateTaskFromTracker()
	{
		// Test: Create a simple bug report (Message1/Text1).
		$this->init();
		$this->clickAndWait("link=Tracker");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", "Summary1");
		$this->type("details", "Description1");
		$this->clickAndWait("document.forms[2].submit[1]");
		$this->assertTextPresent("Summary1");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("");
		$this->assertTextPresent("Description1");

		$this->clickAndWait("link=Build Task Relation");
		$this->clickAndWait("new_task");
		$this->type("details", "Detail summary1 bug");
		$this->type("hours", "5");
		$this->clickAndWait("//td[@id='main']/form/table/tbody/tr[9]/td/input");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Summary1");
		$this->assertTextPresent("[T2] Summary1");
		$this->clickAndWait("link=[T2] Summary1");
		$this->assertTextPresent("Detail summary1 bug");
	}

	function testMaxRowArg()
	{
		// Create a simple bug report with 55 bugs.
		$this->init();
		$this->clickAndWait("link=Tracker");
		$this->clickAndWait("link=Bugs");

		for ($i=1; $i<56; $i++) {
			$this->clickAndWait("link=Submit New");
			$this->type("summary", "Summary$i:");
			$this->type("details", "Description$i");
			$this->clickAndWait("document.forms[2].submit[1]");
		}

		// Default is 25 records only per screen.
		$this->assertTextPresent("Summary1:");
		$this->assertTextPresent("Summary25:");
		$this->assertFalse($this->isTextPresent("Summary26:"));

		$this->clickAndWait("link=[All]");

		$this->assertTextPresent("Summary1:");
		$this->assertTextPresent("Summary25:");
		$this->assertTextPresent("Summary26:");
		$this->assertTextPresent("Summary55:");
	}

	function testUpdateQueries()
	{
		$this->init();
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Build Query");
		$this->type("query_name", "ProjectQuery");
		$this->click("//input[@name='query_type' and @value='2']");
		$this->clickAndWait("submit");

		$this->switchUser('unonmember');

		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");

		$this->clickAndWait("link=Build Query");
		$this->type("query_name", "MyQuery");
		$this->clickAndWait("submit");
		$this->select("query_id", "label=ProjectQuery");
		$this->clickAndWait("run");
		$this->clickAndWait("link=Build Query");
		$this->clickAndWait("submit");
		$this->assertFalse($this->isTextPresent("Error Updating:"));
		$this->assertTextPresent("Query already exists");
	}

	function testPublicQuery()
	{
		$this->init();
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");

		// Create public query
		$this->clickAndWait("link=Build Query");
		$this->click("query_action");
		$this->addSelection("_assigned_to[]", "label=uuser Lastname");
		$this->type("query_name", "uuserquery");
		$this->click("//input[@name='query_type' and @value='1']");
		$this->clickAndWait("submit");

		// Check that the query can be run as anonymous user
		$this->logout();
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->select("query_id", "label=uuserquery");
		$this->clickAndWait("run");

		// Change the query to private
		$this->login('uadmin');
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Build Query");
		$this->select("query_id", "label=uuserquery");
		$this->click("//input[@name='query_type' and @value='0']");
		$this->clickAndWait("submit");

		// Check that the query cannot be run as anonymous user
		$this->logout();
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertFalse($this->isTextPresent("uuserquery"));
	}

	function testEditCustomFields()
	{
		$this->init();
		$this->switchUser('uadmin');

		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");

		$this->clickAndWait("link=Manage Custom Fields");
		$this->clickAndWait("link=[Add/Reorder choices]");
		$this->clickAndWait("//td[@id='main']/form[1]/table/tbody/tr[2]/td[2]/a[2]/img");
		$this->assertTextPresent("Tracker Updated");
		$this->clickAndWait("//td[@id='main']/form[1]/table/tbody/tr[6]/td[2]/a[1]/img");
		$this->assertTextPresent("Tracker Updated");
		$this->clickAndWait("//td[@id='main']/form[1]/table/tbody/tr[5]/td[2]/a[1]/img");
		$this->assertTextPresent("Tracker Updated");
		$this->clickAndWait("post_changes_alphaorder");
		$this->assertTextPresent("Tracker Updated");
		$this->type("order[1]", "8");
		$this->type("order[2]", "7");
		$this->type("order[3]", "6");
		$this->type("order[4]", "5");
		$this->type("order[8]", "4");
		$this->type("order[5]", "3");
		$this->type("order[6]", "2");
		$this->type("order[7]", "1");
		$this->clickAndWait("post_changes_order");
		$this->assertTextPresent("Tracker Updated");
	}

	function testCloneTracker()
	{
		$this->init();

		// Approve the template project first
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Pending projects (new project approval)");
		$this->clickAndWait("document.forms['approve.template'].submit");

		$this->switchUser('uadmin');

		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Administration");

		$this->clickAndWait("link=Clone Tracker");
		$this->assertFalse($this->isTextPresent("Error"));
	}
}
?>
