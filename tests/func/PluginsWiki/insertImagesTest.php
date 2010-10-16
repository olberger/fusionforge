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

class InsertImages extends FForge_SeleniumTestCase
{
	function testinsertImages()
	{
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

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

		$this->open("/wiki/g/projecta/HomePage");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Upload File");
		$this->type("userfile", "/opt/gforge/www/themes/base-alcatel-lucent/images/logo.jpg");
		$this->clickAndWait("//input[@value='Upload']");
		// $this->assertTextPresent("File successfully uploaded.");
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=new page");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "== Image ==\n\n{{logo.jpg}}");
		$this->type("edit-summary", "Image");
		$this->clickAndWait("edit[save]");
	}
}
?>
