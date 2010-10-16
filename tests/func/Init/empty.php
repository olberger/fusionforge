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

class Init_empty extends FForge_SeleniumTestCase
{
	protected function setUp()
	{
		// Reload an empty database before running this test suite.
		system("php ".dirname(dirname(__FILE__))."/db_reload.php");

		$this->setBrowser('*firefox');
		$this->setBrowserUrl(URL);
		$this->setHost(SELENIUM_RC_HOST);
	}

	function testSimpleCreate()
	{
		$this->createProject('ProjectA');

		$this->createUser('uadmin', 103);
		$this->createUser('usupport', 104);
		$this->createUser('ucontrib', 105);
		$this->createUser('ucoredev', 106);
		$this->createUser('uuser', 107);
		$this->createUser('unoaccess', 108); // Member without access
		$this->createUser('unonmember', 109); // Not member 
		
		$this->open("/");
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=ProjectA");
		$this->clickAndWait("link=Project Admin");
		$this->clickAndWait("link=Members");
		$this->type("form_unix_name", "uadmin");
		$this->select("role_id", "label=Admin");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "usupport");
		$this->select("role_id", "label=Community Support Assistant");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "ucontrib");
		$this->select("role_id", "label=Contributor");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "ucoredev");
		$this->select("role_id", "label=Core Developer");
		$this->clickAndWait("adduser");
		$this->type("form_unix_name", "uuser");
		$this->select("role_id", "label=User");
		$this->clickAndWait("adduser");

		// Create a new role 'No Access' with no access on the frs.
		// And put the new user 'noaccess' in this role.
		$this->clickAndWait("link=Add Role");
		$this->type("role_name", "No Access");
		$this->select("data[frs][0]", "label=None");
		$this->clickAndWait("submit");
		$this->type("form_unix_name", "unoaccess");
		$this->select("role_id", "label=No Access");
		$this->clickAndWait("adduser");	
	}
}
?>
