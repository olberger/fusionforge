#!/usr/bin/php
<?php
// Very simple backup script for the PostgresSQL database.
//
// Backup in the backupdir, under the given name as argument.
//
// Alain Peyrat <alain.peyrat@alcatel-lucent.com>

require_once dirname(__FILE__).'/../../src/www/env.inc.php';
require_once $gfcommon.'include/pre.php';

putenv('PGHOST='.forge_get_config('database_host'));
putenv('PGUSER='.forge_get_config('database_user'));
putenv('PGPASSWORD='.forge_get_config('database_password'));

$backupfile = $argv[1];

system("mkdir -p '".dirname($backupfile)."'");

system("pg_dump -O ".forge_get_config('database_name')." -Fc -f '$backupfile'");
