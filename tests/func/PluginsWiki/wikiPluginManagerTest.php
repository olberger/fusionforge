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

class WikiPluginManager extends FForge_SeleniumTestCase
{
	function testwikiPluginManager()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		$this->clickAndWait("link=Home Page");

		// Test of CreateTocPlugin

		// Notes: For CreateToc plugin, check that a Mediawiki table with headers does not interact with the table of contents.
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "<<CreateToc>>\n\n== One ==\n\nOne\n\n== Two ==\n\nTwo\n\n{| class=\"bordered\"\n|+ This is the table caption\n|= This is the table summary\n|-\n! Header A !! Header B !! Header C\n|-\n| Cell A1 || Cell B1 || Cell C1\n|-\n| Cell A2 || Cell B2 || Cell C2\n|-\n| Cell A3 || Cell B3 || Cell C3\n|}\n\n== Three\n\nThree");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		$this->assertTextPresent("Header A");
		$this->assertFalse($this->isTextPresent("!!"));

		// Test of Template plugin

		$this->open("/wiki/g/projecta/foo");
		$this->clickAndWait("link=Create Page");
		$this->type("edit-content", "I am foo version 1.");
		$this->type("edit-summary", "I am foo version 1.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->clickAndWait("link=Edit");
		$this->click("//img[@alt='Bold text [alt-b]']");
		$this->type("edit-content", "I am **foo** version TWO.");
		$this->type("edit-summary", "I am **foo** version TWO.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "I am **foo** version %color=green%THREE%%.");
		$this->click("//img[@alt='Color']");
		$this->type("edit-summary", "I am foo version THREE.");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->open("/wiki/g/projecta/bar");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Create Page");
		$this->type("edit-content", "I am bar. I include foo.\n----\n{{foo}}");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("I am foo version THREE");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "I am bar. I include foo.\n----\n{{foo}}\n----\n{{foo?version=2}}");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("I am foo version TWO");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "I am bar. I include foo.\n----\n<<Template page=foo>>\n----\n<<Template page=foo rev=2>>");
		$this->clickAndWait("edit[save]");
		$this->assertTextPresent("I am foo version THREE");
		$this->assertTextPresent("I am foo version TWO");
	}
}
?>
