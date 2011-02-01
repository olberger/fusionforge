#!/usr/bin/php
<?php
/**
 * FusionForge Installation Dependency Setup
 *
 * Copyright 2006 GForge, LLC
 * Copyright 2011, Alain Peyrat
 * http://fusionforge.org/
 *
 * This file is part of GInstaller, it is called by install.sh.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * FusionForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FusionForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  US
 * Francisco Gimeno
 */

require_once dirname(__FILE__).'/install-common.inc' ;

$args = $_SERVER['argv'];
$hostname = $args[1];

echo "Validating arguments  ";
if (count($args) != 4) {
	echo "FAIL\n  Usage: $args[0] forge.company.com  apacheuser  apachegroup\n";
	exit(127);
}
echo "OK\n";

//validate hostname
echo "Validating hostname  ";
if (!preg_match("/^([[:alnum:]._-])*$/" , $hostname)) {
	echo "FAIL\n  invalid hostname\n";
	exit(2);
}
echo "OK\n";

// #validate apache user
//getent passwd $2 > /dev/null
//found_apacheuser=$?
//if [ $found_apacheuser -ne 0 ]; then
//	echo 1>&2 "invalid apache user"
//	exit 2
//fi

//ARREGLAR ESTO
exec("getent passwd $args[2] > /dev/null", $arr, $t);
if ($t != 0) {
	echo "invalid apache user\n";
	exit(2);
}




// #validate apache group
//getent group $3 > /dev/null
//found_apachegroup=$?
//if [ $found_apachegroup -ne 0 ]; then
//     echo 1>&2 "invalid apache group"
//     exit 2
//fi


exec("getent group $args[3] > /dev/null", $arr, $t);
if ($t != 0) {
	echo "invalid apache group";
	exit(2);
}

mkdir_safe('/etc/gforge');
mkdir_safe('/etc/gforge/plugins');
mkdir_safe('/etc/gforge/httpd.conf.d');
mkdir_safe('/opt/gforge');
mkdir_safe('/var/log/gforge');
mkdir_safe('/var/lib/gforge');
mkdir_safe('/var/lib/gforge/uploads');
mkdir_safe('/var/lib/gforge/scmtarballs');
mkdir_safe('/var/lib/gforge/scmsnapshots');
mkdir_safe('/var/lib/gforge/chroot/scmrepos/svn');
mkdir_safe('/var/lib/gforge/chroot/scmrepos/cvs');
mkdir_safe('/var/lib/gforge/chroot/scmrepos/git');
mkdir_safe('/var/lib/gforge/chroot/scmrepos/hg');
mkdir_safe('/var/lib/gforge/etc');
mkdir_safe('/var/lib/gforge/dumps');
mkdir_safe('/var/lib/gforge/homedirs');
mkdir_safe('/home/groups');

symlink_safe('/home/groups', '/var/lib/gforge/homedirs/groups');
symlink_safe("$fusionforge_data_dir/scmrepos", '/scmrepos');
symlink_safe("$fusionforge_data_dir/scmrepos/svn", '/svnroot');
symlink_safe("$fusionforge_data_dir/scmrepos/cvs", '/cvsroot');

mkdir_safe("$fusionforge_src_dir/www/plugins");
symlink_safe("../plugins/wiki/www/", "$fusionforge_src_dir/www/wiki");
symlink_safe("../../plugins/cvstracker/www/", "$fusionforge_src_dir/www/plugins/cvstracker");
symlink_safe("../../plugins/svntracker/www/", "$fusionforge_src_dir/www/plugins/svntracker");
symlink_safe("../../plugins/scmcvs/www/", "$fusionforge_src_dir/www/plugins/scmcvs");
symlink_safe("../../plugins/fckeditor/www/", "$fusionforge_src_dir/www/plugins/fckeditor");
symlink_safe("../../plugins/blocks/www/", "$fusionforge_src_dir/www/plugins/blocks");

system("cp -r * /opt/gforge");

system("touch /var/lib/gforge/etc/httpd.vhosts");

chdir('/opt/gforge');

//#restricted shell for cvs accounts
system("cp plugins/scmcvs/bin/cvssh.pl /bin/");
system("chmod 755 /bin/cvssh.pl");

if (!is_file("/etc/gforge/httpd.conf")) {
	system("cp etc/httpd.conf-opt /etc/gforge/httpd.conf");
}
$h = opendir ('etc/httpd.conf.d-opt') ;
while (false !== ($file = readdir($h))) {
	if ($file != "."
	&& $file != ".."
	&& (preg_match ('/^[0-9a-zA-Z_-]+(.conf)?$/', $file)
	|| preg_match ('/^[0-9a-zA-Z_-]+(.inc)?$/', $file))) {
		if (!is_file("/etc/gforge/httpd.conf.d/$file")) {
			system("cp etc/httpd.conf.d-opt/$file /etc/gforge/httpd.conf.d");
		}
	}
}
closedir($h);

copy('etc/config.ini-tar', '/etc/gforge/config.ini');

unlink('etc/config.ini.d/defaults.ini');
system("cp -rL etc/config.ini.d /etc/gforge/config.ini.d");

// Install default configuration files for all plugins.
chdir("/opt/gforge/plugins");
foreach( glob("*") as $plugin) {
	$source = "/opt/gforge/plugins/$plugin/etc/plugins/$plugin";
	if (is_dir($source)) {
		system("cp -r $source /etc/gforge/plugins/");
	}
}

$apacheconffiles=array();
if (is_file('/etc/httpd/conf/httpd.conf')) {
	$apacheconffiles[]='/etc/httpd/conf/httpd.conf';
} elseif (is_file('/opt/csw/apache2/etc/httpd.conf')) {
	$apacheconffiles[]='/opt/csw/apache2/etc/httpd.conf';
} elseif (is_file('/etc/apache2/httpd.conf')) {
	$apacheconffiles[]='/etc/apache2/httpd.conf';
} else {
	$apacheconffiles[]='/etc/apache2/sites-enabled/000-default';
}

foreach ($apacheconffiles as $apacheconffile) {
	echo('Setting FusionForge Include For Apache...');
	system("grep \"^Include $fusionforge_etc_dir/httpd.conf\" $apacheconffile > /dev/null", $ret);
	if ($ret == 1) {
		system("echo \"Include $fusionforge_etc_dir/httpd.conf\" >> $apacheconffile");
	}
}

//cd /opt/gforge
chdir("/opt/gforge");
system("chown -R root:$args[3] /opt/gforge");
system("chmod -R 644 /opt/gforge/");
system("cd /opt/gforge && find -type d | xargs chmod 755");
system("chown -R $args[2]:$args[3] /var/lib/gforge/uploads");
system("chmod -R 755 /opt/gforge/cronjobs/");
system("chmod 755 /opt/gforge/www/scm/viewvc/bin/cgi/viewvc.cgi");
system("chmod 755 /opt/gforge/utils/forge_get_config");
system("chmod 755 /opt/gforge/utils/manage-apache-config.sh");
system("chmod 755 /opt/gforge/utils/manage-translations.sh");
system("chmod 755 /opt/gforge/utils/migrate-to-ini-files.sh");

system("chown -R root:$args[3] /etc/gforge/");
system("chmod -R 644 /etc/gforge/");
system("cd /etc/gforge && find -type d | xargs chmod 755");
system("cd /etc/gforge && find -type f -exec perl -pi -e \"s/apacheuser/$args[2]/\" {} \;");
system("cd /etc/gforge && find -type f -exec perl -pi -e \"s/apachegroup/$args[3]/\" {} \;");
system("cd /etc/gforge && find -type f -exec perl -pi -e \"s/gforge\.company\.com/$hostname/\" {} \;");
system("echo \"noreply:	/dev/null\" >> /etc/aliases");

# Generate a random hash for the session_key
$hash = md5(microtime());
system("perl -spi -e \"s/session_key = foobar/session_key = $hash/\" /etc/gforge/config.ini");

print "\n";
