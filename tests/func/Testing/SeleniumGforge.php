<?php
/*
 * Copyright (C) 2007-2008 Alain Peyrat <aljeux at free dot fr>
 * Copyright (C) 2009 Alain Peyrat, Alcatel-Lucent
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

$config = getenv('CONFIG_PHP') ? getenv('CONFIG_PHP'): dirname(dirname(__FILE__)).'/config.php';
require_once $config;

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class FForge_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
	protected $output;

	protected function setUp()
	{
		if (getenv('SELENIUM_RC_DIR') && getenv('SELENIUM_RC_URL')) {
			$this->captureScreenshotOnFailure = true;
			$this->screenshotPath = getenv('SELENIUM_RC_DIR');
			$this->screenshotUrl = getenv('SELENIUM_RC_URL');
		}
		system("php ".dirname(dirname(__FILE__))."/db_load.php");

		$this->setBrowser('*firefox');
		$this->setBrowserUrl(URL);
		$this->setHost(SELENIUM_RC_HOST);
	}

	protected function waitForPageToLoad($timeout)
	{
		parent::waitForPageToLoad($timeout);
		$this->assertElementPresent("//h1");
		// $this->assertFalse($this->isElementPresent("//div[@id='ffErrors']"));
	}

	protected function db($sql)
	{
		system("echo \"$sql\" | psql -q -Upostgres ".DB_NAME);
	}

	protected function cron($cmd)
	{
		system("/usr/bin/php -q -d include_path=.:/etc/gforge:/opt/gforge:/opt/gforge/www/include /opt/gforge/$cmd");
	}

	protected function clearMail() {
		system(dirname(dirname(dirname(__FILE__))).'/scripts/catch_mail.php -c');
	}

	protected function screenshot() {
		$this->captureEntirePageScreenshot(SELENIUM_RC_DIR.'/capture'.get_class().'.png', '');
	}

	protected function getMail() {
		if (file_exists('/tmp/catch_mail.log')) {
			return file_get_contents('/tmp/catch_mail.log');
		}
		else {
			return false;
		}
	}

	protected function fetchMail() {
		$mail = $this->getMail();
		if ($mail !== false) $this->clearMail();
		return $mail;
	}

	protected function init($project='ProjectA', $user='admin')
	{
		$this->open(ROOT);
		$this->clickAndWait("link=$project");

		$this->login($user);

		// For Gforge PHP CLI
		$ret = putenv("GFORGE_WSDL=http://" . SITE . "/soap/index.php?wsdl");
		$this->assertEquals($ret, true);
	}

	protected function initSvn($project='ProjectA', $user='admin')
	{
		// Remove svnroot directory before creating the project.
		$repo = '/svnroot/'.strtolower($project);
		if (is_dir($repo)) {
			system("rm -fr $repo");
		}

		$this->init($project, $user);

		// Run manually the cron for creating the svn structure.
		$this->cron("plugins/websvn/cronjobs/create_svn.php");
	}
	protected function login($username)
	{
		if ($username == 'admin') {
			$password = 'myadmin';
		} else {
			$password = 'password';
		}

		$this->clickAndWait("link=Log In");
		$this->type("form_loginname", $username);
		$this->type("form_pw", $password);
		$this->clickAndWait("login");

	}

	protected function logout()
	{
		$this->clickAndWait("link=Log Out");
	}

	protected function switchUser($username)
	{
		$this->logout();
		$this->login($username);
	}

	protected function createProject ($name) {
		$unix_name = strtolower($name);

		// Create a simple project.
		$this->open( ROOT );
		$this->login('admin');
		$this->clickAndWait("link=My Page");
		$this->clickAndWait("link=Register Project");
		$this->type("full_name", $name);
		$this->type("purpose", "This is a simple description for $name");
		$this->type("description", "This is the public description for $name.");
		$this->type("unix_name", $unix_name);
		$this->clickAndWait("submit");
		$this->assertTextPresent("Your project has been submitted");
		$this->assertTextPresent("you will receive notification of their decision and further instructions");
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Pending (P) (New Project Approval)");
		$this->clickAndWait("document.forms['approve.$unix_name'].submit");
		$this->clickAndWait("link=Home");
		$this->assertTextPresent($name);
		$this->clickAndWait("link=$name");
		$this->assertTextPresent("This is the public description for $name.");
		$this->assertTextPresent("This project has not yet categorized itself");
	}

	protected function createUser ($login, $id)
	{
		$this->open("/");
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Register a New User");
		$this->type("unix_name", $login);
		$this->type("alt_user_name", $login);
		$this->type("password1", "password");
		$this->type("password2", "password");
		$this->type("firstname", $login);
		$this->type("lastname", "Lastname");
		$this->type("email", $login."@debug.log");
		$this->clickAndWait("submit");
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Display Full User List/Edit Users");
		$this->clickAndWait("//a[contains(@href, 'userlist.php?action=activate&user_id=$id')]");
	}

	protected function activatePlugin($pluginName) {
		$this->open( ROOT );
		$this->waitForPageToLoad("30000");
		$this->login('admin');
		$this->clickAndWait("link=Site Admin");
		$this->clickAndWait("link=Plugin Manager");
		$this->click($pluginName);
		$this->click("//a[contains(@href, \"javascript:change('".ROOT."/admin/pluginman.php?update=$pluginName&action=activate','$pluginName');\")]");
		$this->waitForPageToLoad("30000");
		$this->logout();
	}

	protected function activateWiki($project='ProjectA') {
		// Activate the wiki plugin and check that the wiki menu has appeared.

		static $global_active = false;

		// Activate wiki plugin for the forge
		if (!$global_active) {
			$this->clickAndWait("link=Site Admin");
			$this->clickAndWait("link=Plugin Manager");
			$this->click("wiki");
			$this->click("//a[contains(@href, \"javascript:change('".ROOT."/admin/pluginman.php?update=wiki&action=activate','wiki');\")]");
			$this->waitForPageToLoad("30000");
			$global_active = true;
		}

		// Activate wiki plugin for the project
		$this->clickAndWait("link=Home");
		$this->clickAndWait("link=$project");
		$this->clickAndWait("link=Project Admin");

		// Enter the public info and activate the wiki plugin.
		$this->clickAndWait("link=Tools");
		$this->click("use_wiki");
		$this->clickAndWait("submit");
		$this->assertTextPresent("Project information updated");

		$this->clickAndWait("link=Project Summary");

		// Verify that the wiki text is now present.
		$this->assertTextPresent("Wiki");
		$this->click("link=Wiki");
		$this->waitForPageToLoad("90000");
		$this->assertTextPresent("Loading up virgin wiki");
		$this->assertTextPresent("Complete");
		$this->clickAndWait("link=Wiki");

	}

	protected function CLI($command)
	{
		exec("echo y | " . CLI_CMD . " $command", $output, $ret);
		$this->output = join("\n", $output);
		return $ret;
	}

	protected function Jagosi($command, $pipe="")
	{
		$value = exec(JAGOSI_CMD . "/$command -U http://" . SITE . "/soap/ $pipe", $output, $ret);
		$this->output = $output;
		$this->assertEquals($ret, 0);
		return $value;
	}

	protected function getOutput()
	{
		return $this->output;
	}
	}

	?>
