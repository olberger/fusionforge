<?php
/**
 *
 * Project Admin: Edit Packages
 *
 * Copyright 1999-2001 (c) VA Linux Systems
 * The rest Copyright 2002-2004 (c) GForge Team
 * http://gforge.org/
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

require_once('../../env.inc.php');
require_once $gfwww.'include/pre.php';	
require_once $gfwww.'frs/include/frs_utils.php';
require_once $gfcommon.'frs/FRSPackage.class.php';

$group_id = getIntFromRequest('group_id');
$package_id = getIntFromRequest('package_id');
if (!$group_id) {
	exit_no_group();
}

$project =& group_get_object($group_id);
if (!$project || $project->isError()) {
	exit_error('Error',$project->getErrorMessage());
}
$perm =& $project->getPermission ();
if (!$perm->isReleaseTechnician()) {
	exit_permission_denied();
}

$frsp = new FRSPackage($project,$package_id);
if (!$frsp || !is_object($frsp)) {
	exit_error('Error','Could Not Get FRS Package');
} elseif ($frsp->isError()) {
	exit_error('Error',$frsp->getErrorMessage());
}

/*
	Relatively simple form to delete packages of releases
*/

frs_admin_header(array('title'=>sprintf(_('Delete Package: %1$s'), $frsp->getName()),'group'=>$group_id));

	echo '
	<form action="/frs/admin/?group_id='.$group_id.'" method="post">
	<p>
	<input type="hidden" name="func" value="delete_package" />
	<input type="hidden" name="package_id" value="'. $package_id .'" />
	'._('You are about to permanently and irretrievably delete this package and all its releases and files!').'
        </p> 
        <p> 
	<input type="checkbox" name="sure" value="1" />'._('I\'m Sure').'
        </p> 
        <p> 
	<input type="checkbox" name="really_sure" value="1" />'._('I\'m Really Sure').'
        </p>
        <p>
	<input type="submit" name="submit" value="'._('Delete').'" />
        </p> 
	</form>';

frs_admin_footer();

?>
