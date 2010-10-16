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

class ListTrackers extends FForge_SeleniumTestCase
{
	function testListTrackers()
	{
		$this->init();

		// Create 10 tracker entries
		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");

		for ($i=1; $i<=10; $i++) {
			$this->clickAndWait("link=Submit New");
			$this->type("summary", "Summary$i");
			$this->type("details", "Description$i");
			$this->clickAndWait("document.forms[2].submit[1]");
			$this->assertTextPresent("Summary$i");
			$this->clickAndWait("link=Summary$i");
			$this->assertTextPresent("Description$i");
		}

		$ret = $this->CLI("login --username=uadmin --password=password --project=projecta");
		$this->assertEquals($ret, 0);

		// Use CLI to get the tracker id
		$ret = $this->CLI("tracker typelist | grep 'Bug Tracking System' | awk '{print $2}'");
		$this->assertEquals($ret, 0);
		$type = $this->getOutput();
		$this->assertEquals($type, 101);

		// Use CLI to get the list of bugs in given tracker
		$ret = $this->CLI("tracker list --type=$type | grep 'Summary' | wc -l");
		$this->assertEquals($ret, 0);
		$number = $this->getOutput();
		$this->assertEquals($number, 10);

		// Use CLI to get the tracker id
		$ret =$this->CLI("tracker typelist | grep 'Feature Request Tracking System ' | awk '{print $2}'");
		$this->assertEquals($ret, 0);
		$type = $this->getOutput();
		$this->assertEquals($type, 104);

		// Use CLI to get the list of bugs in given tracker
		$ret = $this->CLI("tracker list --type=$type | grep 'Summary' | wc -l");
		$this->assertEquals($ret, 0);
		$number = $this->getOutput();
		$this->assertEquals($number, 0);
	}
}
?>
