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

class CreateTrackerEntry extends FForge_SeleniumTestCase
{
	function testCreateTrackerEntry()
	{
		$this->init();

		$ret = $this->CLI("login --username=uadmin --password=password --project=projecta");
		$this->assertEquals($ret, 0);

		// Use CLI to get the tracker id
		$ret = $this->CLI("tracker typelist | grep 'Bug Tracking System' | awk '{print $2}'");
		$this->assertEquals($ret, 0);
		$type = $this->getOutput();
		$this->assertEquals(101, $type);

		// Use CLI to create a bug in given tracker
		$ret = $this->CLI("tracker add --type=$type --priority=4 --summary='New bug' --details='This is a brand new bug' | tail -n 2 | head -n 1 | awk '{print $2}'");
		$this->assertEquals($ret, 0);
		$id = $this->getOutput();
		$this->assertEquals("1", $id);

		// Check the new bug is present via Web browser

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent("New bug");
		$this->clickAndWait("link=New bug");
		$this->assertTextPresent("This is a brand new bug");

		// Use CLI to create a bug in given tracker. Use special characters.
		$ret = $this->CLI("tracker add --type=$type --priority=4 --summary=\"It's a bug\" --details=\"It's a\\tbrand new bug\" | tail -n 2 | head -n 1 | awk '{print $2}'");
		$this->assertEquals($ret, 0);
		$id = $this->getOutput();
		$this->assertEquals("2", $id);

		// Check the new bug is present via Web browser

		$this->clickAndWait("link=Trackers");
		$this->clickAndWait("link=Bugs");
		$this->assertTextPresent("It's a bug");
		$this->clickAndWait("link=It's a bug");
		$this->assertTextPresent("It's a\\tbrand new bug");
	}
}
?>
