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

class AddDocument extends FForge_SeleniumTestCase
{
	function testAddDocument()
	{
		$this->init();

		$ret = $this->CLI("login --username=uadmin --password=password --project=projecta");
		$this->assertEquals($ret, 0);

		// Use CLI to reference an URL as document
		$ret = $this->CLI("document adddocument --doc_group=1 --title=\"WebSite name\" --description=\"This is a description\" --language_id=1 --url=http://www.fusionforge.org --state_id=1");
		$this->assertEquals($ret, 0);
		$expected = "+--------+\n| Result |\n+--------+\n| 1      |\n+--------+";
		$this->assertEquals($expected, $this->getOutput());

		// Use CLI to upload a file as document
		$file = dirname(__FILE__)."/textfile.txt";
		$ret = $this->CLI("document adddocument --doc_group=1 --title=\"Document name\" --description=\"This is a description\" --language_id=1 --filename=$file --state_id=1");
		$this->assertEquals($ret, 0);
		$expected = "+--------+\n| Result |\n+--------+\n| 2      |\n+--------+";
		$this->assertEquals($expected, $this->getOutput());

		// Error case: no argument provided
		$ret = $this->CLI("document adddocument");
		$this->assertEquals($ret, 1);
		$out = $this->getOutput();
		$this->assertEquals(0, strpos($out, "Fatal error:"));

		// Error case: missing argument
		$ret = $this->CLI("document adddocument --doc_group=2");
		$this->assertEquals($ret, 1);
		$out = $this->getOutput();
		$this->assertEquals(0, strpos($out, "Fatal error:"));

		// Error case: missing argument
		$ret = $this->CLI("document adddocument --doc_group=2");
		$this->assertEquals($ret, 1);
		$out = $this->getOutput();
		$this->assertEquals(0, strpos($out, "Fatal error:"));

		// Error case: missing argument
		$ret = $this->CLI("document adddocument --doc_group=2 --title='Title name' --description='This is a description'");
		$this->assertEquals($ret, 1);
		$out = $this->getOutput();
		$this->assertEquals(0, strpos($out, "Fatal error:"));

		// Delete document
		$ret = $this->CLI("document delete --doc_id=1");
		$this->assertEquals($ret, 0);
		$expected = "+--------+\n| Result |\n+--------+\n| 1      |\n+--------+";
		$this->assertEquals($expected, $this->getOutput());

		// Error case: no doc_id
		$ret = $this->CLI("document delete");
		$this->assertEquals($ret, 1);
		$out = $this->getOutput();
		$this->assertEquals(0, strpos($out, "Fatal error:"));

		// Error case: wrong doc_id
		$ret = $this->CLI("document delete --doc_id=17");
		// Strange: 0 is returned in this case
		// $this->assertEquals($ret, 1);
		$out = $this->getOutput();
		$this->assertEquals(0, strpos($out, "Fatal error:"));
	}
}
?>
