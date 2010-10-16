<?php

require_once dirname(dirname(__FILE__)).'/Testing/SeleniumGforge.php';

class WikiPurge extends FForge_SeleniumTestCase
{
	function testwikiPurge()
	{
		$this->init();
		$this->activateWiki();

		// Purge pages using Wiki Admin
		$this->clickAndWait("link=Wiki");
		$this->clickAndWait("link=Wiki Admin");
		$this->clickAndWait("//input[@value='WikiAdminSelect']");
		$this->click("All");
		$this->click("p[titlebar]");
		$this->click("p[WatchPage]");
		$this->click("p[WhoIsOnline]");
		$this->clickAndWait("wikiadmin[WikiAdminPurge]");
		$this->clickAndWait("admin_purge[purge]");
		$this->assertTextPresent("3 pages have been permanently purged:");
		$this->assertTextPresent("WhoIsOnline");
		$this->assertTextPresent("WatchPage");
		$this->assertTextPresent("titlebar");
		$this->clickAndWait("link=Special Pages");
		$this->clickAndWait("link=AllPages");
		$this->assertFalse($this->isTextPresent("WhoIsOnline"));
		$this->assertFalse($this->isTextPresent("WatchPage"));
		$this->assertFalse($this->isTextPresent("titlebar"));

		// Purge page using the Purge tab
		$this->clickAndWait("link=Home Page");
		$this->clickAndWait("link=SandBox");
		$this->clickAndWait("link=Purge");
		$this->assertTextPresent("You are about to purge 'SandBox'!");
		$this->assertTextPresent("You can try out Wiki in here.");
		$this->clickAndWait("verify");
		$this->assertTextPresent("Purged page 'SandBox' successfully.");
		$this->clickAndWait("link=Home Page");
		$this->assertTextPresent("SandBox?");
		$this->clickAndWait("link=exact:?");
		$this->assertTrue($this->isElementPresent("link=Create Page"));
	}
}
?>
