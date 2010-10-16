<?php

define ('SELENIUM_RC_HOST', 'vamos');
define ('URL', 'http://acos.alcatel-lucent.com/');
define ('GFLOGIN', '@@GFLOGIN@@');
define ('GFPASSWD', '@@GFPASSWD@@');
define ('DBLOGIN', '@@DBLOGIN@@');
define ('DBPASSWD', '@@DBPASSWD@@');
define ('ROOT', '');
define ('PROJECTS', '/tmp/groups_using_wiki.list');

require_once 'Testing/SeleniumGforge.php';
require_once 'PHPUnit/Framework/TestCase.php';

class UpgradeWiki extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
		$this->verificationErrors = array();
		$this->selenium = new Testing_SeleniumGforge($this, "*firefox", URL, SELENIUM_RC_HOST);
        $result = $this->selenium->start();
    }

    function tearDown()
    {
        $this->selenium->stop();
    }

    function testUpgradeWiki()
    {
		$this->selenium->open( ROOT );
    	$this->selenium->click("link=Log In");
//    	$this->selenium->click("link=S'identifier");
    	$this->selenium->waitForPageToLoad("30000");
		$this->selenium->type("form_loginname", GFLOGIN );
		$this->selenium->type("form_pw", GFPASSWD );
		$this->selenium->click("login");
		$this->selenium->waitForPageToLoad("30000");

		$text = file_get_contents( PROJECTS );
		foreach ( explode("\n", $text) as $p) {
			$p = trim($p);
			if ($p) {
				echo "Upgrading: '$p'\n"; 
				$this->selenium->open("/wiki/g/$p/PhpWikiAdministration?action=upgrade&overwrite=1");
			    $this->selenium->type("dbadmin[user]", DBLOGIN );
			    $this->selenium->type("dbadmin[passwd]", DBPASSWD );
			    $this->selenium->click("//input[@value='Submit']");
			    $this->selenium->waitForPageToLoad("90000");
			}
		}
    }
}
?>
