<?php
 
class SeleniumRemoteSuite extends PHPUnit_Framework_TestSuite
{
	protected function setUp()
	{
		system("cd scripts; ./start_vm.sh centos52");
		system("scp ../../build/packages/fusionforge-*allinone.tar.bz2 root@centos52:");
		system("ssh root@centos52 'tar jxf fusionforge-*allinone.tar.bz2'");
		system("ssh root@centos52 'cd fusionforge-*; FFORGE_RPM_REPO=http://buildbot.fusionforge.org:8080/cruisecontrol/artifacts/fusionforge-trunk/current/packages/ FFORGE_DB=fforge FFORGE_USER=gforge FFORGE_ADMIN_USER=ffadmin FFORGE_ADMIN_PASSWORD=ffadmin ./install.sh centos52.local'");

		system("ssh root@centos52 'mkdir -p /opt/tests/func'");
		system("scp func/db_reload.php root@centos52:/opt/tests/func/");
		system("scp func/config.php.tests root@centos52:/opt/tests/func/config.php");

		system("scp /usr/share/php/PHPUnit/Extensions/SeleniumTestCase/*pend.php root@centos52:/opt/tests");
		system("scp /usr/share/php/PHPUnit/Extensions/SeleniumTestCase/phpunit_coverage.php root@centos52:/opt/gforge/www");
		//system("ssh root@centos52 'perl -spi -e \'s!^auto_prepend_file.*!auto_prepend_file=/opt/tests/prepend.php!\' /etc/php.ini');
		//system("ssh root@centos52 'perl -spi -e \'s!^auto_append_file.*!auto_append_file=/opt/tests/append.php!\' /etc/php.ini');

	}

	protected function tearDown()
	{
		system("cd scripts; ./stop_vm.sh centos52");
	}
}
?>
