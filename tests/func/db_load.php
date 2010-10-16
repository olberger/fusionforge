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

if ( DB_TYPE == 'mysql') {
	// Reload a fresh database before running this test suite.
	system("mysqladmin -f -u".DB_USER." -p".DB_PASSWORD." drop ".DB_NAME." &>/dev/null");
	system("mysqladmin -u".DB_USER." -p".DB_PASSWORD." create ".DB_NAME);
	system("mysql -u".DB_USER." -p".DB_PASSWORD." ".DB_NAME." < ".dirname(dirname(dirname(__FILE__)))."/gforge/db/gforge-struct-mysql.sql");
	system("mysql -u".DB_USER." -p".DB_PASSWORD." ".DB_NAME." < ".dirname(dirname(dirname(__FILE__)))."/gforge/db/gforge-data-mysql.sql");
} elseif ( DB_TYPE == 'pgsql') {
	system("psql -q -U".DB_USER." ".DB_NAME." -f ".dirname(dirname(dirname(__FILE__)))."/src/db/reset_schema.sql &>/tmp/gforge-init.log");
	system("pg_restore ".dirname(__FILE__)."/gforge-init.csql | psql -q -U".DB_USER." ".DB_NAME." &>/tmp/gforge-import.log");
	system("echo \"UPDATE groups SET homepage='".SITE."/www/projecta/', http_domain='".SITE."/www/projecta/', scm_box='".SITE."' WHERE group_id=6\" | psql -q -U".DB_USER." ".DB_NAME." &>/tmp/gforge-import2.log");
	system("echo \"VACUUM FULL ANALYZE;\" | psql -q -Upostgres ".DB_NAME);
} else {
	print "Unsupported database type: ".DB_TYPE. "\n";
	exit;
}

?>
