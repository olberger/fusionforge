<?php
/**
 * FusionForge Documentation Manager
 *
 * Copyright 2000, Quentin Cregan/Sourceforge
 * Copyright 2002-2003, Tim Perdue/GForge, LLC
 * Copyright 2010-2011, Franck Villaume - Capgemini
 * Copyright (C) 2011 Alain Peyrat - Alcatel-Lucent
 * http://fusionforge.org
 *
 * This file is part of FusionForge. FusionForge is free software;
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or (at your option)
 * any later version.
 *
 * FusionForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with FusionForge; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

/* please do not add require here : use www/docman/index.php to add require */
/* global variables used */
global $group_id; // id of the group
global $nested_docs;
global $linkmenu;
global $g; // the group object

if (!forge_check_perm('docman', $group_id, 'read')) {
	$return_msg= _('Document Manager Access Denied');
	session_redirect('/docman/?group_id='.$group_id.'&warning_msg='.urlencode($return_msg));
}

/**
 * needed for docman_recursive_display function call
 * see utils.php for more information
 */
$idExposeTreeIndex = 0;
$idhtml = 0;

if ($g->usesPlugin('projects_hierarchy')) {
	$projectsArray = array($g->getID());
} else {
	$projectsArray = array($g->getID());
}
foreach ($projectsArray as $projectID) {
	echo '<div id="documenttree" style="height:100%">';
	$groupObject = group_get_object($projectID);
	$dm = new DocumentManager($g);
	$dm->getJSTree($linkmenu);
	echo '<noscript>';
	$dm->getTree($linkmenu);
	echo '</noscript>';
	echo '</div>';
}

?>
