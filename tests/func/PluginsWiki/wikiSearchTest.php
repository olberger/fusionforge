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

class WikiSearch extends FForge_SeleniumTestCase
{
	function testwikiSearch()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		// Search in the wiki with Gforge search
		$this->type("words", "auto");
		$this->select("type_of_search", "label=This projects's wiki");
		$this->clickAndWait("Search");
		$this->assertTextPresent("Search results for auto");
		$this->assertTrue($this->isElementPresent("link=FindPage"));
		$this->assertTrue($this->isElementPresent("link=FullTextSearch"));
		$this->assertTrue($this->isElementPresent("link=TitleSearch"));
		$this->assertEquals("PhpWikiAdministration", $this->getText("link=PhpWikiAdministration"));

		// Search in the wiki with Wiki full text search
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Search");
		$this->type("//div[@id='content']/div[1]/form[2]/input[2]", "auto");
		$this->clickAndWait("//input[@value='FullTextSearch' and @type='submit']");
		$this->assertTextPresent("PhpWikiAdministration");
		$this->assertTextPresent("Do some verification checks and upgrade changes automatically,");
	}
}
?>
