<?php
/**
 * FusionForge logout page
 *
 * Copyright 1999-2001 (c) VA Linux Systems
 *
 * This file is part of FusionForge.
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

Header( "Expires: Wed, 11 Nov 1998 11:11:11 GMT");
Header( "Cache-Control: no-cache");
Header( "Cache-Control: must-revalidate");

require_once('../env.inc.php');
require_once $gfcommon.'include/pre.php';

$return_to = getStringFromRequest('return_to');

//
//      Validate return_to
//
if ($return_to) {
        $tmpreturn=explode('?',$return_to);
        if (!@is_file(forge_get_config('url_root').$tmpreturn[0]) && !@is_dir(forge_get_config('url_root').$tmpreturn[0]) && !(strpos($tmpreturn[0],'projects') == 1) && !(strpos($tmpreturn[0],'mediawiki') == 1)) {
	$return_to='';
}				        }

session_logout();

plugin_hook('before_logout_redirect');

if ($return_to) {
	header('Location: '.$return_to);
}else{
	header('Location: '.util_make_url ('/'));
}
?>
