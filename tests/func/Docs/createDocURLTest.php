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

class CreateDocURL extends FForge_SeleniumTestCase
{
	function testCreateDocURL()
	{
		$this->init();
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Documents");
		$this->clickAndWait("link=Submit new");
		$this->type("title", "My document");
		$this->type("ed", "4");
		$this->select("statusid", "label=IN PREPARATION");
		$this->type("description", "L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->type("file_url", "http://vargenau.free.fr");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Document submitted sucessfully");
		$this->assertEquals("My document", $this->getText("link=My document"));
		$this->assertTextPresent("L'année dernière à Noël, 3 < 4, 中国 \" <em>, père & fils");
		$this->assertTextPresent("IN PREPARATION");
		$this->clickAndWait("link=My document");
		$this->assertEquals("Bienvenue sur le site de Marc-Etienne Vargenau", $this->getTitle());

		$this->open(ROOT);
		$this->select("none", "label=projecta");
		$this->waitForPageToLoad("30000");
		$this->clickAndWait("link=Documents");
		$this->click("link=Uncategorized Submissions");
		$this->clickAndWait("link=[ Delete ]");
		$this->assertTextPresent("You are about to permanently delete this document.");
		$this->click("sure");
		$this->click("really_sure");
		$this->clickAndWait("post_changes");
		$this->assertTextPresent("Deleted");
		$this->assertTextPresent("This project has no visible documents");
	}
}
?>
