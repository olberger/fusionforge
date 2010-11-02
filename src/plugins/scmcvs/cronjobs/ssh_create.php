#! /usr/bin/php
<?php
/**
 * GForge Cron Job
 *
 * The rest Copyright 2002-2005 (c) GForge Team
 * Copyright (C) 2009  Sylvain Beucler
 * http://gforge.org/
 *
 * This file is part of FusionForge.
 *
 * GForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require dirname(__FILE__).'/../../env.inc.php';
require_once $gfcommon.'include/pre.php';
require $gfcommon.'include/cron_utils.php';

$err='';

$res=db_query_params ('SELECT user_name,user_id,authorized_keys 
	FROM users 
	WHERE authorized_keys != $1
	AND status=$2 AND unix_status = $3',
		      array('',
			    'A',
			    'A'));

for ($i=0; $i<db_numrows($res); $i++) {


	$ssh_key=db_result($res,$i,'authorized_keys');
	$username=db_result($res,$i,'user_name');
	$dir = forge_get_config('homedir_prefix').'/'.$username;
	if (util_is_root_dir($dir)) {
		$err .=  "Error! homedir_prefix/username Points To Root Directory!";
		continue;
	}
	$uid=db_result($res,$i,'user_id');

	$ssh_key=str_replace('###',"\n",$ssh_key);
	$uid += 1000;

	$ssh_dir = "forge_get_config('homedir_prefix')/$username/.ssh";
	if (!is_dir($ssh_dir)) {
		mkdir ($ssh_dir, 0755);
	}

	# Set the effective uid/gid to this user, so as to avoid symlink attacks
	$userinfo = posix_getpwnam($username);
	posix_setegid($userinfo['gid']);
	posix_seteuid($userinfo['uid']);
	$h8 = fopen("$ssh_dir/authorized_keys","w");
	fwrite($h8,'# This file is automatically generated from your account settings.'."\n");
	fwrite($h8,$ssh_key);
	fclose($h8);
	posix_seteuid(0);
	posix_setegid(0);
		
	system("chown $username:users forge_get_config('homedir_prefix')/$username");
	system("chown $username:users $ssh_dir");
	system("chmod 0644 $ssh_dir/authorized_keys");
	system("chown $username:users $ssh_dir/authorized_keys");

}

cron_entry(15,$err);

?>
