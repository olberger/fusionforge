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

class WikiRateIt extends FForge_SeleniumTestCase
{
	function testwikiRateIt()
	{
		$index = 0;
		// Test: Activate the wiki plugin and check that the wiki menu has appeared.
		$this->init();
		$this->activateWiki();

		// RateIt is not activated by default
		$this->open("/wiki/g/projecta/HomePage");
		$this->waitForPageToLoad("30000");
		$this->assertFalse($this->isTextPresent("Rating: 0 (0 vote)"));

		// Activate RateIt
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Tools");
		$this->clickAndWait("link=Wiki Admin");
		$this->click("config[ENABLE_RATEIT]");
		$this->clickAndWait("//input[@value='Save Configuration']");
		$this->assertTextPresent("Configuration saved.");
		$this->clickAndWait("link=Wiki");
		$this->assertTextPresent("Rating: 0 (0 vote)");

		$this->clickAndWait("link=Home Page");
		// Rate page
		$this->click('RateIt2455771270'.$index.'5');
		$this->assertTextPresent("Rating: 2.5 (1 vote)");
		// Change rating of page
		$this->click('RateIt2455771270'.$index.'8');
		$this->assertTextPresent("Rating: 4 (1 vote)");
		// Cancel rating of page
		$this->click('RateIt2455771270'.$index.'BStarCancel');
		$this->assertTextPresent("Rating: 0 (0 vote)");

		// Let uuser rate some pages
		$this->switchUser("uuser");
		$this->open("/wiki/g/projecta/HomePage");
		$this->waitForPageToLoad("30000");
		$this->click("RateIt245577127005");
		$this->clickAndWait("link=Special Pages");
		$this->click("RateIt653680825010");
		$this->clickAndWait("link=Upload File");
		$this->click("RateIt295510325504");
		$this->clickAndWait("link=User Preferences");
		$this->click("RateIt59166749606");
		$this->open("/wiki/g/projecta/MyRatings");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Here are your 4 page ratings:");

		// Let ucontrib rate some pages
		$this->switchUser("ucontrib");
		$this->open("/wiki/g/projecta/HomePage");
		$this->waitForPageToLoad("30000");
		$this->click("RateIt245577127007");
		$this->assertTextPresent("Rating: 3 (2 votes)");
		$this->clickAndWait("link=Search");
		$this->click("RateIt160736168004");
		$this->clickAndWait("link=User Preferences");
		$this->click("RateIt591667496010");
		$this->assertTextPresent("Rating: 4 (2 votes)");
		$this->open("/wiki/g/projecta/MyRatings");
		$this->waitForPageToLoad("30000");
		$this->assertTextPresent("Here are your 3 page ratings:");
	}
}
?>
