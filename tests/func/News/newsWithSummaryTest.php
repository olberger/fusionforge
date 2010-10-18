<?php
/*
 * Copyright (C) 2010 Marc-Etienne Vargenau, Alcatel-Lucent
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

class NewsWithSummary extends FForge_SeleniumTestCase
{
	function testNewsWithSummary()
	{
		$this->init();
		$this->switchUser('uadmin');
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=News");
		$this->assertTextPresent("No News Found for ProjectA");

		// Create 20 news
		for ($i=0; $i<20; $i++) {
			$this->clickAndWait("link=Submit");
			$this->type("summary", "News number $i");
			$this->type("details", "News $i, line 1.\nNews $i, line 2.\nNews $i, line 3.\nNews $i, line 4.\nNews $i, line 5.\nNews $i, line 6.\n");
			$this->clickAndWait("submit");
			$this->assertTextPresent("News Added");

			// Add a delay to avoid having news 9 and 10 having the same timestamp.
			if ($i == 9) sleep(1);
		}

		$this->clickAndWait("link=News");

		// Check the title of all news is present.
		for ($i=0; $i<20; $i++) {
			$this->assertTextPresent("News number $i");
		}

		// Check the third line of last 10 news is present
		for ($i=10; $i<20; $i++) {
			$this->assertTextPresent("News $i, line 3");
		}

		// Check the first line of first 10 news is NOT present
		for ($i=0; $i<10; $i++) {
			$this->assertFalse($this->isTextPresent("News $i, line 1"));
		}

		// Check the fifth line of all news is NOT present
		for ($i=0; $i<20; $i++) {
			$this->assertFalse($this->isTextPresent("News $i, line 5"));
		}
	}
}
?>
