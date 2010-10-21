#!/usr/bin/php
<?php
# Very simple backup script for the PostgresSQL database.
#
# Backup in the backupdir, under the name '<table>-<day>.tar.gz'.
#
# The use of day is to rotate the backups (keeping the last 7 backups).
#
# Alain Peyrat <alain.peyrat@alcatel-lucent.com>

//require_once dirname(__FILE__).'/../../src/www/env.inc.php';
//require_once $gfcommon.'include/pre.php';
//
//putenv('PGHOST='.forge_get_config('database_host'));
//putenv('PGUSER='.forge_get_config('database_user'));
//putenv('PGPASSWORD='.forge_get_config('database_password'));

require_once dirname(__FILE__).'/../func/config.php';
 
putenv('PGUSER='.DB_USER);
putenv('PGPASSWORD='.DB_PASSWORD);

if (isset($argv[1])) {
	$backupfile = $argv[1];
	$backupdir = dirname($backupfile);
} else {
	$backupdir="/opt/backups/pg_backup/";
	$backupfile=$backupdir.'/'.DB_NAME.'-'.date('d').'.csql';
}

system("mkdir -p '$backupdir'");

system("pg_dump -O ".DB_NAME." -f '$backupfile'");

if (!isset($argv[1])) {
	system("find '$backupdir' -name '.DB_NAME.-*.tar*' -mtime +7 -exec rm {} \\;");
	system("find '$backupdir' -name '.DB_NAME.-*.csql' -mtime +7 -exec rm {} \\;");
}
