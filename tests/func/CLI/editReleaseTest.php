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

class EditRelease extends FForge_SeleniumTestCase
{
	function testEditRelease()
	{
		$this->init();

		$ret = $this->CLI("login --username=uadmin --password=password --project=projecta");
		$this->assertEquals($ret, 0);

		// Use CLI to get the package id
		$ret = $this->CLI("frs packages | grep 'projecta' | awk '{print $2}'");
		$this->assertEquals($ret, 0);
		$id = $this->getOutput();
		$this->assertEquals($id, 1);

		// Use CLI to create a release for the package
		$ret = $this->CLI("frs addrelease --package=$id --name='My new release'");
		$this->assertEquals($ret, 0);

		// Use CLI to get the list of releases in given package
		$ret = $this->CLI("frs releases --package=$id | grep 'My new release' | wc -l");
		$this->assertEquals($ret, 0);
		$number = $this->getOutput();
		$this->assertEquals($number, 1);

		$file = dirname(__FILE__).'/textfile.txt';

		// Use CLI to add a text file to a release of a given package.
		$ret = $this->CLI("frs addfile --package=$id --release=1 --file=$file");
		$this->assertEquals($ret, 0);

		$tmpfname = tempnam("/tmp", "cli");
		unlink($tmpfname);

		// Retrieve file on the standard output using CLI
		$ret = $this->CLI("frs getfile --package=$id --release=1 --id=1 >$tmpfname");
		$this->assertEquals($ret, 0);

		// Check that the downloaded file is the same as the initial file.
		$this->assertFileExists($tmpfname);
		$this->assertFileEquals($file, $tmpfname);
		unlink($tmpfname);

		// Retrieve file in a file using CLI using the output option.
		$ret = $this->CLI("frs getfile --package=$id --release=1 --id=1 --output=$tmpfname");
		$this->assertEquals($ret, 0);

		// Check that the downloaded file is the same as the initial file.
		$this->assertFileExists($tmpfname);
		$this->assertFileEquals($file, $tmpfname);
		unlink($tmpfname);

		// Redo the same test with a binary file.
		// Redo the test by trying to get a private file.
	}
}
?>
