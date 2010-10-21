<?php
/*
 * Create a initial database suitable for running the tests.
 * The initial database is made of:
 *  The initial database given by the package.
 *  + An admin account (login: admin, password: myadmin)
 *  + A simple project (projectA)
 */

require_once dirname(__FILE__).'/config.php';

if ( !CONFIGURED ) {
	print "File 'config.php' is not correctly configured, abording.\n";
	exit(1);
}

system("echo \"DROP SCHEMA public CASCADE;CREATE SCHEMA public;\" | psql -q -Upostgres ".DB_NAME." &>/tmp/gforge-init.log");
system("echo \"GRANT ALL ON SCHEMA public TO ".DB_USER.";\" | psql -q -Upostgres ".DB_NAME);
system("psql -q -U".DB_USER." ".DB_NAME." -f ".dirname(__FILE__)."/gforge-init.sql &>/tmp/gforge-import.log");
system("echo \"UPDATE groups SET homepage='".SITE."/www/projecta/', http_domain='".SITE."/www/projecta/', scm_box='".SITE."' WHERE group_id=6\" | psql -q -U".DB_USER." ".DB_NAME." &>/tmp/gforge-import2.log");
system("echo \"VACUUM FULL ANALYZE;\" | psql -q -Upostgres ".DB_NAME);

?>
