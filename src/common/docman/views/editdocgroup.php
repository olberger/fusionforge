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
global $g; //group object
global $group_id; // id of the group
global $dirid; // id of doc_group
global $dgf; // document directory factory of this group
global $dgh; // document directory html

if (!forge_check_perm('docman', $group_id, 'approve')) {
	$return_msg= _('Document Manager Access Denied');
	session_redirect('/docman/?group_id='.$group_id.'&warning_msg='.urlencode($return_msg));
}

$dg = new DocumentGroup($g, $dirid);
if ($dg->isError())
	session_redirect('/docman/?group_id='.$group_id.'&error_msg='.urlencode($dg->getErrorMessage()));

?>
<div class="docmanDivIncluded">
	<form name="editgroup" action="?group_id=<?php echo $group_id; ?>&action=editdocgroup" method="post">
		<input type="hidden" name="dirid" value="<?php echo $dirid; ?>" />
		<table>
			<tr>
				<td><?php echo _('Directory Name') ?></td>
				<td><input type="text" name="groupname" value="<?php echo $dg->getName(); ?>" /></td>
				<td>&nbsp;</td>
				<td><?php echo _('belongs to') ?></td>
				<td>
<?php
$dgh->showSelectNestedGroups($dgf->getNested(), "parent_dirid", true, $dg->getParentId(), array($dg->getID()));
?>
				</td>
				<td><input type="submit" value="<?php echo _('Edit') ?>" name="submit" /></td>
			</tr>
		</table>
		<p>
		<?php echo _('Directory name will be used as a title, so it should be formatted correspondingly.') ?>
		</p>
	</form>
</div>
