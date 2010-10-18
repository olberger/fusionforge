#!/usr/bin/php
<?php
# Very simple backup script for the PostgresSQL database.
#
# Backup in the backupdir, under the name '<table>-<day>.tar.gz'.
#
# The use of day is to rotate the backups (keeping the last 7 backups).
#
# Alain Peyrat <alain.peyrat@alcatel-lucent.com>

require_once dirname(__FILE__).'/../../src/www/env.inc.php';
require_once $gfcommon.'include/pre.php';

putenv('PGHOST='.forge_get_config('database_host'));
putenv('PGUSER='.forge_get_config('database_user'));
putenv('PGPASSWORD='.forge_get_config('database_password'));

if (isset($argv[1])) {
	$backupfile = $argv[1];
	$backupdir = dirname($backupfile);
} else {
	$backupdir="/opt/backups/pg_backup/".forge_get_config('database_host');
	$backupfile=$backupdir.'/'.forge_get_config('database_name').'-'.date('d').'.csql';
}

system("mkdir -p '$backupdir'");

system("pg_dump -O ".forge_get_config('database_name')." -Fc -f '$backupfile'");

if (!isset($argv[1])) {
	system("find '$backupdir' -name '.forge_get_config('database_name').-*.tar*' -mtime +7 -exec rm {} \\;");
	system("find '$backupdir' -name '.forge_get_config('database_name').-*.csql' -mtime +7 -exec rm {} \\;");
}
