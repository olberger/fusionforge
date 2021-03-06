<?php 
/**
 * This file is (c) Copyright 2011 by Sabri LABBENE, Institut TELECOM
 *
 * This file is part of FusionForge.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * This program has been developed in the frame of the HELIOS
 * project with financial support of its funders.
 *
 */

class CompactRessource {
	
	public function __construct(){

	}
	
	public function compactUserLink($username, $user_id) {
		$ressource_uri = util_make_url('/plugins/oslc/compact/user/'.$username);
		$url = '<a href="'. util_make_url_u ($username, $user_id) . '"' .
		' onmouseover="hover(\''. $ressource_uri . '\', \'compact_user_' . $username . '\');" onmouseout="closeHover();">' . 
		$username . '</a>';
		// Add div that will contain the popup
		$url .= '<div id="compact_user_'.$username.'"></div>';
		return $url;
	}
}
?>