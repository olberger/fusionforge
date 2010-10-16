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

class FrontpageChoice extends FForge_SeleniumTestCase
{
  function testFrontpageChoice() {
  	$this->init();
	$this->activateWiki();
	
	 $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
	
	// Project activity
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Project Activity");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Project Activity");
	
	// Forums
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Forums");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Forums for ProjectA");
	
	// Trackers
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Trackers");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Trackers for ProjectA");
	
	// Mailing lists
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Mailing Lists");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Mailing Lists for ProjectA");
	
	// Tasks
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Tasks");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Subprojects and Tasks");
	
	// Documents
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Documents");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Documents for ProjectA");
	
	// Surveys
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Surveys");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Surveys for ProjectA");
	
	// News
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=News");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("News");
	
	// Sourse code
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Source Code");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("SCM Repository");
	
	// File release system
    $this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=File Release System");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Below is a list of all files of the project");
	
	// Wiki
	$this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Wiki");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("Welcome to the wiki dedicated to your project");
	
	// Summary
	$this->clickAndWait("link=Project Admin");
    $this->select("frontpage", "label=Project Summary");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=ProjectA");
    $this->assertTextPresent("This is the public description for ProjectA");
  }
}
?>
