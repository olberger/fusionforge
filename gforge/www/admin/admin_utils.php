<?php
/**
 * Module of support routines for Site Admin
 *
 * Copyright 1999-2001 (c) VA Linux Systems
 *
 * This file is part of GForge.
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

function site_admin_header($params) {
	session_require_global_perm ('forge_admin');

	if (get_magic_quotes_gpc()) {
		$GLOBALS['warning_msg'] = 'WARNING: Your installation is running with php magic_quotes_gpc ON, please change to OFF';
	}
	if (ini_get('register_globals')) {
		$GLOBALS['warning_msg'] = 'WARNING: Your installation is running with php register_globals ON, this is very unsecure, please change to OFF';
	}
	site_header($params);
}

function site_admin_footer($params) {
	site_footer($params);
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
