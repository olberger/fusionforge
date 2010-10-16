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

class CreateNews extends FForge_SeleniumTestCase
{
	function testMonitorProjectNews()
	{
		$this->init();

		// Create initial news.
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", "Initial news");
		$this->type("details", "We need an initial news before monitoring.");
		$this->clickAndWait("submit");

		// Monitor project news
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Monitor News");
		$this->assertTextPresent("News monitoring started");

		// Create a simple news.
		$this->clickAndWait("link=Submit");
		$this->type("summary", "First news");
		$this->type("details", "This is a simple news.");
		$this->clickAndWait("submit");

		// Stop monitoring project news
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Stop monitoring News");
		$this->assertTextPresent("News monitoring deactivated");

		// Create a simple news.
		$this->clickAndWait("link=Submit");
		$this->type("summary", "Second news");
		$this->type("details", "This is another text");
		$this->clickAndWait("submit");

		// Create a news with funny characters
		$this->clickAndWait("link=Submit");
		$this->type("summary", "title: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->type("details", "body: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->clickAndWait("submit");

		// Read the news with funny characters
		$this->clickAndWait("link=News");
		$this->click("link=title: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("body: L'année à Noël, 3 < 4, 中国 \" <em>, père & fils");
	}

	function testMyTestCase()
	{
		// Create a simple news.
		$this->init();
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", "First news");
		$this->type("details", "This is a simple news.");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=News");
		$this->assertTextPresent("First news");
		$this->clickAndWait("link=First news");
		$this->assertTextPresent("First news");
		$this->assertTextPresent("This is a simple news.");

		// Create a second news.
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", "Second news");
		$this->type("details", "This is another text");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Second news");
		$this->assertTextPresent("Second news");
		$this->assertTextPresent("This is another text");
		
		// Check that news are visible in the activity
		// TODO: Not implemented in gforge-4.6
//		$this->click("link=Project Activity");
//		$this->waitForPageToLoad("30000");
//		$this->assertTextPresent("First news");
//		$this->assertTextPresent("Second news");
		
		// Check modification of a news.
		$this->clickAndWait("link=News");
		$this->click("//a[contains(@href, '" . ROOT . "/news/admin/?group_id=6')]");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Second news");
		$this->type("details", "This is another text (corrected)");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Second news");
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Second news");
		$this->assertTextPresent("This is another text (corrected)");
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", "Test3");
		$this->type("details", "Special ' chars \"");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Test3");
		$this->assertTextPresent("Special ' chars \"");
		$this->clickAndWait("link=News");
		$this->click("//a[contains(@href, '". ROOT . "/news/admin/?group_id=6')]");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Test3");
		$this->click("document.forms[2].status[1]");
		$this->clickAndWait("submit");

	}

	/*
	 * Test multilines news formated in HTML.
	 */
	function testAcBug4100()
	{
		// Create a simple news.
		$this->init();
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", "Multi line news");
		$this->type("details", "<p>line1</p><p>line2</p><p>line3</p><br />hello<p>line5</p>\n");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=News");
		$this->assertTextPresent("Multi line news");
		$this->assertTextPresent("line1");
		$this->assertTextPresent("line2");
		$this->assertTextPresent("line3");
		$this->assertTextPresent("hello");
		// $this->assertFalse($this->isTextPresent("line5"));
		$this->clickAndWait("link=Multi line news");
		$this->assertTextPresent("Multi line news");
		$this->assertTextPresent("line1");
		$this->assertTextPresent("line2");
		$this->assertTextPresent("line3");
		$this->assertTextPresent("hello");
		$this->assertTextPresent("line5");
	}
	
	/*
	 * Test multiple post of the news (reload).
	 * Test skipped due to manual intervention required.
	 */
	function skiptestPreventMultiplePost()
	{
		// Create a simple news.
		$this->init();
		$this->clickAndWait("link=News");
		$this->clickAndWait("link=Submit");
		$this->type("summary", "My ABC news");
		$this->type("details", "hello DEF with a long detail.\n");
		$this->clickAndWait("submit");
		$this->assertTextPresent("News Added."); 
		$this->chooseOkOnNextConfirmation();
		// Problem, a confirmation window is displayed and I didn't found
		// the way to automatically click on the Ok button.
		$this->refresh();
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Error - double submit"); 
	}
	
}
?>
