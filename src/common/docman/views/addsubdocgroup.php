<?php
/**
 * FusionForge Documentation Manager
 *
 * Copyright 2000, Quentin Cregan/Sourceforge
 * Copyright 2002-2003, Tim Perdue/GForge, LLC
 * Copyright 2010-2011, Franck Villaume - Capgemini
 * http://fusionforge.org
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

/* please do not add require here : use www/docman/index.php to add require */
/* global variables used */
global $group_id; // id of the group
global $dirid; //id of the doc_group

if (!forge_check_perm('docman', $group_id, 'approve')) {
	$return_msg= _('Document Manager Access Denied');
	session_redirect('/docman/?group_id='.$group_id.'&warning_msg='.urlencode($return_msg));
}

?>
<script type="text/javascript">
function doItAddSubGroup() {
	document.getElementById('addsubgroup').submit();
	document.getElementById('submitaddsubgroup').disabled = true;
}
</script>
<?php
echo '<div class="docmanDivIncluded" >';
echo '<form id="addsubgroup" name="addsubgroup" method="post" action="?group_id='.$group_id.'&action=addsubdocgroup&dirid='.$dirid.'">';
if ($dirid) {
	echo _('Name of the document subdirectory to create:'). ' ';
} else {
	echo _('Name of the document directory to create:'). ' ';
}
echo '<input type="text" name="groupname" size="40" maxlength="255" >';
echo '<input id="submitaddsubgroup" type="button" value="'. _('Create') .'" onclick="javascript:doItAddSubGroup()" />';
echo '</form>';
echo '</div>';
?>
