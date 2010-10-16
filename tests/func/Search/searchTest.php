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

class Search extends FForge_SeleniumTestCase
{
	protected $stringToSearch = "L'année à Noël, 3 < 4, 中国 \" <em>, père & fils";
	protected $alreadyCreated = 0;

	protected function create() {
		if (! $this->alreadyCreated) {
			$this->init();

			$this->_createNews();
			$this->_createTracker();
			$this->_createThreadInForum();
			$this->_createTask();
			$this->_createRelease();
			$this->_createDocument();

			$this->alreadyCreated = 1;
		}
	}

	function doAllSearch()
	{
		$this->testSearchProject();
		$this->testSearchPeople();
		$this->testSearchLatestNews();
		$this->testSearchTrackers();
		$this->testSearchForums();
		//$this->testSearchMailingLists();
		$this->testSearchTasks();
		$this->testSearchReleases();
		$this->testSearchDocuments();
		$this->testSearchNews();
	}

	function testSearchProject() {
		$this->init();
		$this->clickAndWait("link=Home");
		$this->select("type_of_search", "label=Project");
		$this->type("words", "projecta");
		$this->clickAndWait("Search");
		$this->assertTextPresent("This is the public description for ProjectA");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchPeople() {
		$this->init();
		$this->clickAndWait("link=Home");
		$this->select("type_of_search", "label=People");
		$this->type("words", "admin");
		$this->clickAndWait("Search");
		$this->assertTextPresent("uadmin Lastname");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchLatestNews() {
		$this->init();
		$this->_createNews();
		$this->clickAndWait("link=Home");
		$this->select("type_of_search", "label=Latest News");
		$this->type("words", $this->stringToSearch . " news01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " news01");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchTrackers() {
		$this->init();
		$this->_createTracker();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->select("type_of_search", "label=This project's trackers");
		$this->type("words", $this->stringToSearch . " tracker01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " tracker01");
		$this->assertFalse($this->isTextPresent("No matches found"));

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->select("type_of_search", "label=Bugs");
		$this->type("words", $this->stringToSearch . " tracker01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " tracker01");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchForums() {
		$this->init();
		$this->_createThreadInForum();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->select("type_of_search", "label=This project's forums");
		$this->type("words", $this->stringToSearch . " thread01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " thread01");
		$this->assertFalse($this->isTextPresent("No matches found"));

		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->select("type_of_search", "label=This forum");
		$this->type("words", $this->stringToSearch . " thread01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " thread01");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	//function testSearchMailingLists() {
	//
	//}

	function testSearchTasks() {
		$this->init();
		$this->_createTask();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->select("type_of_search", "label=This project's tasks");
		$this->type("words", $this->stringToSearch . " task01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " task01");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchReleases() {
		$this->init();
		$this->_createRelease();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->select("type_of_search", "label=This project's releases");
		$this->type("words", $this->stringToSearch . " release01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " release01");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchDocuments() {
		$this->init();
		$this->_createDocument();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");

		$this->select("type_of_search", "label=This project's documents");
		$this->type("words", $this->stringToSearch . " document01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " document01");
		$this->assertFalse($this->isTextPresent("No matches found"));

		// Search Part Number
		$this->select("type_of_search", "label=This project's documents");
		$this->type("words", "978-2723472449");
		$this->clickAndWait("Search");
		$this->assertTextPresent("document01");
		$this->assertTextPresent("978-2723472449");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchNews() {
		$this->init();
		$this->_createNews();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->select("type_of_search", "label=This project's news");
		$this->type("words", $this->stringToSearch . " news01");
		$this->clickAndWait("Search");
		$this->assertTextPresent($this->stringToSearch . " news01");
		$this->assertFalse($this->isTextPresent("No matches found"));
	}

	function testSearchInAll()
	{
		$this->create();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->select("type_of_search", "label=Search the entire project");
		$this->type("words", $this->stringToSearch);
		$this->clickAndWait("Search");
		$this->assertTextPresent("Tracker Search Result");
		$this->assertTextPresent($this->stringToSearch . " tracker01");
		$this->assertTextPresent("Forum Search Result");
		$this->assertTextPresent($this->stringToSearch . " thread01");
		$this->assertTextPresent("Mailing List Search Result");
		//$this->assertTextPresent($this->stringToSearch . " mailinglist01");
		$this->assertTextPresent("Task Search Result");
		$this->assertTextPresent($this->stringToSearch . " task01");
		$this->assertTextPresent("Documentation Search Result");
		$this->assertTextPresent($this->stringToSearch . " document01");
		$this->assertTextPresent("Files Search Result");
		$this->assertTextPresent($this->stringToSearch . " release01");
		$this->assertTextPresent("News Search Result");
		$this->assertTextPresent($this->stringToSearch . " news01");
	}

	function _createNews() {
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", $this->stringToSearch . " news01 title");
		$this->type("details", $this->stringToSearch . " news01 body");
		$this->clickAndWait("submit");
		$this->assertTextPresent("News Added");
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Approve/Reject");
		$this->click("link=".$this->stringToSearch . " news01 title");
		$this->waitForPageToLoad("30000");
		$this->click("status");
		$this->clickAndWait("submit");
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent($this->stringToSearch . " news01");
		$this->clickAndWait("link=News");
		$this->assertTextPresent($this->stringToSearch . " news01");
	}

	function _createTracker() {
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->clickAndWait("link=Submit New");
		$this->type("summary", $this->stringToSearch . " tracker01 title");
		$this->type("details", $this->stringToSearch . " tracker01 body");
		$this->clickAndWait("submit");
		$this->assertTextPresent("regexp:Item \[#[0-9]+\] successfully created");
		/*
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent($this->stringToSearch . " tracker01");
		*/
	}

	function _createThreadInForum() {
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->clickAndWait("link=Start New Thread");
		$this->type("subject", $this->stringToSearch . " thread01 title");
		$this->type("body", $this->stringToSearch . " thread01 body");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Message Posted Successfully");
		/*
		$this->clickAndWait("link=Forums");
		$this->clickAndWait("link=open-discussion");
		$this->assertTextPresent($this->stringToSearch . " thread01");
		*/
	}

	function _createTask() {
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->clickAndWait("link=Add Task");
		$this->type("summary", $this->stringToSearch . " task01 title");
		$this->type("details", $this->stringToSearch . " task01 body");
		$this->type("hours", "1");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Task Created Successfully");
		/*
		$this->clickAndWait("link=Tasks");
		$this->clickAndWait("link=To Do");
		$this->assertTextPresent($this->stringToSearch . " task01");
		*/
	}

	function _createRelease() {
		$this->clickAndWait("link=File Release System");
		$this->clickAndWait("link=Create a New Release");
		$this->type("release_name", $this->stringToSearch . " release01");
		$this->type("userlink", "http://www.fusionforge.org");
		$this->clickAndWait("submit");
		$this->assertTextPresent("File Released");
	}

	function _createDocument() {
		$this->clickAndWait("link=Documents");
		$this->clickAndWait("link=Administration");
		$this->clickAndWait("link=Add/Edit Document Groups");
		$this->type("groupname", "group1");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Submit new");
		$this->type("title", $this->stringToSearch . " document01 title");
		$this->type("code", "978-2723472449");
		$this->type("description", $this->stringToSearch . " document01 description");
		$this->type("file_url", "http://www.fusionforge.org");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Document submitted sucessfully");
	}
}

?>
