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

class ScanUrl extends FForge_SeleniumTestCase
{
	function testScanForge()
	{
		$this->init();

		$this->open(ROOT."/account/login.php/%22%3e%3cscript%3edocument.write(18000+739)%3c/script%3e");
		$this->assertFalse($this->isTextPresent("18739"));

		try {
			$this->open(ROOT."/include/");
		} catch (Exception $e) {
			$this->assertTrue(strpos($e->getMessage(), "Forbidden") !== false);
			$this->assertTrue(strpos($e->getMessage(), "403") !== false);
		}
	}

	function testScanWiki()
	{
		$this->init();
		$this->activateWiki();

		$this->clickAndWait("link=HomePage");

		$this->open(ROOT."/wiki/g/projecta/TitleSearch?s=toto'");
		$this->assertFalse($this->isTextPresent("Fatal Error:"));

		$this->open(ROOT."/wiki/g/projecta/%22%3e%3cscript%3edocument.write(18000+739)%3c/script%3e");
		$this->assertFalse($this->isTextPresent("18739"));

		$this->open(ROOT."/wiki/g/projecta/FindPage?action=TitleSearch&s=toto%27&regex=auto");
		$this->assertFalse($this->isTextPresent("Fatal Error:"));

		$this->open(ROOT."/wiki/g/projecta/TitleSearch?action=FullTextSearch&s=WFXSSProbe%27%22)/>&case_exact=1&regex=sql");
		$this->assertFalse($this->isTextPresent("Fatal Error:"));

		$this->open(ROOT."/wiki/g/projecta/WikiAdminSelect?s=1234<%00script>alert(%22Watchfire%20XSS%22)</script>");
		$this->assertFalse($this->isTextPresent("Fatal Error:"));

		$this->open(ROOT."/wiki/g/projecta/</title><script>document.write(18000+739)</script><title>");
		$this->assertFalse($this->isTextPresent("18739"));

		$this->open(ROOT."/wiki/g/projecta/AllPages/%3cimg%20src%3d%22javascript%3adocument.write(18000+739)%22%3e");
		$this->assertFalse($this->isTextPresent("18739"));
	}
}
?>
