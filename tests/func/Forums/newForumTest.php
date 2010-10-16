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

class NewForum extends FForge_SeleniumTestCase
{
  function testNewForum()
  {
    $this->init();

    // Create a new forum
    $this->clickAndWait("link=Forums");
    $this->clickAndWait("link=open-discussion");
    $this->clickAndWait("link=Administration");
    $this->clickAndWait("link=Add forum");
    $this->type("forum_name", "My-new-forum");
    $this->type("description", "My new forum description");
    $this->clickAndWait("submit");
    // Check creation
    $this->assertTextPresent("Forum created successfully");
    // Check new forum is listed
    $this->clickAndWait("link=Forums");
    $this->assertTrue($this->isElementPresent("link=my-new-forum"));
    $this->assertTextPresent("My new forum description");
    $this->clickAndWait("link=my-new-forum");

    // Create a new discussion (thread)
    $this->clickAndWait("link=Start New Thread");
    $this->type("subject", "My Subject");
    $this->type("body", "My message.");
    $this->clickAndWait("submit");
    $this->clickAndWait("link=My Subject");
    // Reply
    $this->clickAndWait("link=[ reply ]");
    $this->type("body", "My reply to the forum.");
    $this->clickAndWait("submit");
    $this->assertTextPresent("Message Posted Successfully");
    $this->assertEquals("1", $this->getText("//td[@id='main']/table[1]/tbody/tr[1]/td[3]"));

    // Browse in flat mode
    $this->select("style", "label=Flat");
    $this->clickAndWait("submit");
    // Browse in nested mode
    $this->select("style", "label=Nested");
    $this->clickAndWait("submit");
    $this->assertTextPresent("My reply to the forum.");
    // Browse in threaded mode
    $this->select("style", "label=Threaded");
    $this->clickAndWait("submit");
    // Browse in ultimate mode
    $this->select("style", "label=Ultimate");
    $this->clickAndWait("submit");
    $this->assertEquals("My Subject", $this->getText("//td[@id='main']/table[1]/tbody/tr[1]/td[1]"));
  }
}
?>
