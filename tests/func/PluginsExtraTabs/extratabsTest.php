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

class ExtraTabs extends FForge_SeleniumTestCase
{
  protected $alreadyActive = 0;
	
  function testExtraTabs()
  {
    // Activate Extra Tabs plugin at Forge level
    $this->_activateExtraTabsPlugin();
	
    $this->init();

    // Go to Project Admin
    $this->select("none", "label=projecta");
    $this->waitForPageToLoad("30000");
    $this->clickAndWait("link=Project Admin");
    $this->clickAndWait("link=Tools");
    $this->clickAndWait("link=Extra Tabs Admin");

    // Add an extra tab
    $this->type("tab_name", "Fusion Forge");
    $this->type("tab_url", "http://www.fusionforge.org");
    $this->clickAndWait("//input[@value='Add tab']");
    $this->assertTextPresent("Tab successfully added");
    $this->assertEquals("Fusion Forge", $this->getText("link=Fusion Forge"));

    // Try to create the same extra tab
    $this->type("tab_name", "Fusion Forge");
    $this->type("tab_url", "http://www.fusionforge.org");
    $this->clickAndWait("//input[@value='Add tab']");
    $this->assertFalse($this->isTextPresent("Tab successfully added"));
    
    // Try to modify the extra tab with empty field
    $this->select("index", "label=Fusion Forge");
    $this->clickAndWait("modify");
    $this->assertTextPresent("Nothing done");
    
    // Try to rename the extra tab
    $this->select("index", "label=Fusion Forge");
    $this->type("tab_rename", "Fusion Forge 2");
    $this->clickAndWait("modify");
    $this->assertTextPresent("Tab successfully renamed");
    $this->assertTextPresent("Fusion Forge 2");

    // Try to modify URL of the extra tab with malformed URL
    $this->select("index", "label=Fusion Forge 2");
    $this->type("tab_new_url", "www.fusionforge2.org");
    $this->clickAndWait("modify");
    $this->assertTextPresent("ERROR: Malformed URL");
    
    // Try to modify URL of the extra tab
    $this->select("index", "label=Fusion Forge 2");
    $this->type("tab_new_url", "http://www.fusionforge2.org");
    $this->clickAndWait("modify");
    $this->assertTextPresent("URL successfully changed");
    
    // Try to modify both name and URL
    $this->select("index", "label=Fusion Forge 2");
    $this->type("tab_rename", "Fusion Forge");
    $this->type("tab_new_url", "http://www.fusionforge.org");
    $this->clickAndWait("modify");
    $this->assertTextPresent("Tab successfully renamed");
    $this->assertTextPresent("URL successfully changed");
    
    // Delete it
    $this->clickAndWait("delete");
    $this->assertTextPresent("Tab successfully deleted");

    // Try to add an empty extra tab
    $this->type("tab_name", "");
    $this->type("tab_url", "http://www.fusionforge.org");
    $this->clickAndWait("//input[@value='Add tab']");
    $this->assertFalse($this->isTextPresent("Tab successfully added"));

    // Try to add an extra tab without URL
    $this->type("tab_name", "Fusion Forge");
    $this->type("tab_url", "");
    $this->clickAndWait("//input[@value='Add tab']");
    $this->assertFalse($this->isTextPresent("Tab successfully added"));

    // Try to add an extra tab with an incorrect URL
    $this->type("tab_name", "Fusion Forge");
    $this->type("tab_url", "zorglub://www.fusionforge.org");
    $this->clickAndWait("//input[@value='Add tab']");
    $this->assertFalse($this->isTextPresent("Tab successfully added"));
  }

  private function _activateExtraTabsPlugin() {
  	if (! $this->alreadyActive) {
  		$this->activatePlugin('extratabs');
		$this->alreadyActive = 1;
  	}
  }
}
?>
