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

class WikiActivity extends FForge_SeleniumTestCase
{

	/*
	 * This test does not work, the goal was to generate an history to
	 * check the activity and graph.
	*/
	function testWikiActivity()
	{
		$this->init();
		$this->activateWiki();

		$this->clickAndWait("link=Project Summary");

		// system("date 01121030"); // Set date to 12 Jan 10h30

		$this->clickAndWait("link=Reporting");
		$this->clickAndWait("link=Initialize / Rebuild Reporting Tables");
		$this->click("im_sure");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Successfully Rebuilt");

/*
		// Verify that the wiki text is now present.
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->assertTextPresent("Wiki");
		$this->click("link=Wiki");
		$this->waitForPageToLoad("90000");

		system("date 01130000"); // Set date to 12 Jan 10h30
		$this->cron("cronjobs/site_stats.php");

		system("date 02081030"); // Set date to 12 Jan 10h30

		// Add HomePage in the watchpage and edit HomePage after.
		$this->clickAndWait("link=HomePage");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "modified in febuary");
		$this->type("edit-summary", "testing watchlist");
//		$this->click("edit-minor_edit");
		$this->clickAndWait("edit[save]");

		system("date 02090000"); // Set date to 09 Feb 00h00
		$this->cron("cronjobs/site_stats.php");

		system("date 03071030"); // Set date to 7 Mar 10h30

		// Add HomePage in the watchpage and edit HomePage after.
		$this->clickAndWait("link=HomePage");
		$this->clickAndWait("link=Edit");
		$this->type("edit-content", "once more in march");
		$this->type("edit-summary", "testing");
		$this->clickAndWait("edit[save]");

		system("date 03080000"); // Set date to 8 March 00h00
		$this->cron("cronjobs/site_stats.php");

		system("date 03130800"); // Set date to 13 March 08h00 (strange ?)
*/
	}
}
?>
