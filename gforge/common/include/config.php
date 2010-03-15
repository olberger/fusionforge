<?php
/**
 * FusionForge configuration functions
 *
 * Copyright 2009, Roland Mas
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 * 
 * FusionForge is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FusionForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */

if (!isset ($fusionforge_config)) {
	$fusionforge_config = array () ;
}

function get_config ($section, $var) {
	if (!isset ($fusionforge_config[$section])) {
		return false ;
	}

	if (!isset ($fusionforge_config[$section][$var])) {
		return false ;
	}

	return $fusionforge_config[$section][$var] ;
}

function read_config_file ($file) {
	// Initial implementation reuses values from local.inc:
	$fusionforge_config['core']['forge_name'] = $GLOBALS['sys_name'] ;
	return ;

	// Real implementation should read *.ini files
	/* Pseudo-code:
	 $sections = ini_parse ($file) ;
	 foreach ($sections as $sectname => $options) {
	 	foreach ($options as $key => $value) {
			$fusionforge_config[$sectname][$key] = $value ;
	 	}
	 }
	*/
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
