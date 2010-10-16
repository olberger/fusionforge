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

class NewWikiPage extends FForge_SeleniumTestCase
{
	function testnewWikiPage()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		// Check initial number of pages
		$this->clickAndWait("link=Special Pages");
		$this->clickAndWait("link=AllPages");
		$this->assertTextPresent("All pages in this wiki (77 total)");

		$this->open("/wiki/g/projecta/HomePage");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Upload File");
		$this->type("userfile", "/opt/gforge/www/themes/base-alcatel-lucent/images/logo.jpg");
		$this->clickAndWait("//input[@value='Upload']");

		$this->open("/wiki/g/projecta/draftpage");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Create Page");
		$this->type("edit-content", "this page is draft");
		$this->type("edit-summary", "template");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{colorbox\n|text=<strong>Welcome to the wiki dedicated to your project!</strong>\n|color=#90EE90\n|bordercolor=green\n}}\n\n{| cellpadding=5\n|- valign=top\n| width=50% | {{titlebar|title=What is a wiki?}}\n\nThe wiki will allow you to easily create web pages to share information about your project. You can experiment in the [[SandBox]].\n\n| width=50% | {{titlebar|title=I need help}}\n\n* Learn about [[Help:AddingPages|adding pages]].\n* Check [[Help:TextFormattingRules|text formatting rules]].\n* Browse all [[Help:../Help|Help pages]].\n\n|}\n\n== New page ==\n\nI create a [[new page]].");
		$this->type("edit-summary", "I create a [[new page]].");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->clickAndWait("link=exact:?");
		$this->type("edit-content", "This is my page.");
		$this->click("edit-summary");
		$this->type("edit-summary", "First page");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");

		// page title must contain wiki page name
		$this->assertTitle("*new page");
		$this->assertElementPresent("//h1");
		$this->assertText("//h1", "new page");

		// Check bold text
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "This is **bold text**.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Bold");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("This is bold text.");
		$this->assertElementPresent("//div[@class='wikitext']/p/strong", "bold text");

		// Check italics
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "This is //italics//.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Italics");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("This is italics.");
		$this->assertElementPresent("//div[@class='wikitext']/p/em", "italics");

		// Check bold italics
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "Mix them at will: **//bold italics//**");
		$this->click("edit-summary");
		$this->type("edit-summary", "Bold italics");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("Mix them at will: bold italics");
		$this->assertElementPresent("//div[@class='wikitext']/p/strong/em", "bold italics");

		// Check monospace
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "This is ##monospace text##.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Monospace");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("This is monospace text.");
		$this->assertElementPresent("//div[@class='wikitext']/p/tt", "monospace text");

		// Check superscript
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "The XX^^th^^ century.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Superscript");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("The XXth century.");
		$this->assertElementPresent("//div[@class='wikitext']/p/sup", "th");

		// Check subscript
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "Water is H,,2,,O.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Subscript");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("Water is H2O.");
		$this->assertElementPresent("//div[@class='wikitext']/p/sub", "2");

		// Check bullet list
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "A bullet list:\n* one,\n* two,\n* three.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Bullet list");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/ul/li", "two");

		// Check numbered list
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "A numbered list:\n# one,\n# two,\n# three.");
		$this->click("edit-summary");
		$this->type("edit-summary", "Numbered list");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/ol/li", "two");

		// Check Wikicreole table
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "A Wikicreole table:\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |");
		$this->click("edit-summary");
		$this->type("edit-summary", "Wikicreole table");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/table");
		$this->assertElementPresent("//th", "Heading Col 1");
		$this->assertElementPresent("//td", "Two lines");
		$this->assertElementPresent("//td/br");

		// Check Wikicreole images
		// Step 1: only image in paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{logo.jpg}}");
		$this->click("edit-summary");
		$this->type("edit-summary", "Wikicreole image");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");
		// Step 2: one image inside paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "foo {{logo.jpg}} bar");
		$this->click("edit-summary");
		$this->type("edit-summary", "Wikicreole image");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");
		// Step 3: only 2 images in paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{logo.jpg}}{{logo.jpg}}");
		$this->click("edit-summary");
		$this->type("edit-summary", "Wikicreole image");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");
		// Step 4: two images inside paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "foo {{logo.jpg}} bar {{logo.jpg}} baz");
		$this->click("edit-summary");
		$this->type("edit-summary", "Wikicreole image");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");

		// Check predefined icons
		// Step 1: only icon in paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{:)}}");
		$this->click("edit-summary");
		$this->type("edit-summary", "Predefined icon");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");
		// Step 2: one icon inside paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "foo {{(+)}} bar");
		$this->click("edit-summary");
		$this->type("edit-summary", "Predefined icon");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");
		// Step 3: only 2 icons in paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{(on)}}{{(off)}}");
		$this->click("edit-summary");
		$this->type("edit-summary", "Predefined icon");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");
		// Step 4: two icons inside paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "foo {{(*)}} bar {{(*r)}} baz");
		$this->click("edit-summary");
		$this->type("edit-summary", "Predefined icon");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertElementPresent("//div[@class='wikitext']/p/img");

		// Check templates
		// Step 1: only template in paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{draftpage}}");
		$this->click("edit-summary");
		$this->type("edit-summary", "template");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("this page is draft");
		// Step 2: one template inside paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "foo {{draftpage}} bar");
		$this->click("edit-summary");
		$this->type("edit-summary", "template");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("foo this page is draft bar");
		// Step 3: only 2 templates in paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "{{draftpage}}{{draftpage}}");
		$this->click("edit-summary");
		$this->type("edit-summary", "template");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("this page is draftthis page is draft");
		// Step 4: two templates inside paragraph
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "foo {{draftpage}} bar {{draftpage}} baz");
		$this->click("edit-summary");
		$this->type("edit-summary", "template");
		$this->clickAndWait("//input[@name='edit[save]' and @value='Save' and @type='submit']");
		$this->assertTextPresent("foo this page is draft bar this page is draft baz");
  }
}
?>
