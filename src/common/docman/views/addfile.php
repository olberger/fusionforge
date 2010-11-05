<?php

/**
 * FusionForge Documentation Manager
 *
 * Copyright 2000, Quentin Cregan/Sourceforge
 * Copyright 2002-2003, Tim Perdue/GForge, LLC
 * Copyright 2010, Franck Villaume - Capgemini
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
global $g; // group object
global $group_id; // id of the group
global $dirid; //id of the doc_group
global $dgh; // document group html object
global $gdf; // document grou factory object

if ( $dgf->getNested() == NULL ) {
	echo '<div class="docmanDivIncluded">';
	echo '<div class="warning">'. _('You MUST first create at least one directory to store your document.') .'</p></div>';
} else {
	/* display the add new documentation form */
?>
	<script type="text/javascript" >
	function displayRowFile() {
	    document.getElementById('filerow').style.display = '';
        document.getElementById('urlrow').style.display = 'none';
        document.getElementById('editrow').style.display = 'none';
        document.getElementById('editnamerow').style.display = 'none';
	}
	function displayRowUrl() {
	    document.getElementById('filerow').style.display = 'none';
        document.getElementById('urlrow').style.display = '';
	    document.getElementById('editrow').style.display = 'none';
	    document.getElementById('editnamerow').style.display = 'none';
	}
	function displayRowEditor() {
	    document.getElementById('filerow').style.display = 'none';
        document.getElementById('urlrow').style.display = 'none';
	    document.getElementById('editrow').style.display = '';
	    document.getElementById('editnamerow').style.display = '';
	}
	</script>
<?php
	echo '<div class="docmanDivIncluded">';
	echo '<p>'. _('<strong>Document Title</strong>:  Refers to the relatively brief title of the document (e.g. How to use the download server)<br /><strong>Description:</strong> A brief description to be placed just under the title<br />') .'</p>';

	if ($g->useDocmanSearch()) 
		echo '<p>'. _('Both fields are used by document search engine.'). '</p>';

	echo '<form name="adddata" action="?group_id='.$group_id.'&action=addfile" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td style="text-align:right;">
						<strong>'. _('Document Title').'</strong>'.utils_requiredField()
                	.'</td><td>'
                    	.'&nbsp;<input type="text" name="title" size="40" maxlength="255" />&nbsp;'
                    	.sprintf(_('(at least %1$s characters)'), 5)
					.'</td>
				</tr>
				<tr>
					<td style="text-align:right;">
						<strong>'. _('Description') .'</strong>'.utils_requiredField()
                	.'</td><td>'
                    	.'&nbsp;<input type="text" name="description" size="50" maxlength="255" />&nbsp;'
                    	.sprintf(_('(at least %1$s characters)'), 10)
					.'</td>
				</tr>
				<tr>
					<td style="text-align:right;">
						<strong>'. _('Type of Document') .'</strong>'.utils_requiredField()
                	.'</td><td>
					<input type="radio" name="type" value="httpupload" onClick="javascript:displayRowFile()" />'. _('File') .'<input type="radio" name="type" value="pasteurl" onClick="javascript:displayRowUrl()" />'. _('URL');
	if ($g->useCreateOnline()) {
   		echo '<input type="radio" name="type" value="editor" onClick="javascript:displayRowEditor()" />'. _('Create online');
	}
	echo '			</td>
				</tr>
				<tr id="filerow" style="display:none">
					<td style="text-align:right;">
						<strong>'. _('Upload File') .'</strong>'. utils_requiredField()
                	.'</td><td>'
						.'&nbsp;<input type="file" name="uploaded_data" size="30" />
					</td>
				</tr>
				<tr id="urlrow" style="display:none">
					<td style="text-align:right;">
						<strong>'. _('URL') .'</strong>'. utils_requiredField()
                	.'</td><td>'
						.'&nbsp;<input type="text" name="file_url" size="30" />
					</td>
				</tr>
				<tr id="editnamerow" style="display:none">
					<td style="text-align:right;">
						<strong>'. _('File Name') .'</strong>'. utils_requiredField()
                	.'</td><td>'
						.'&nbsp;<input type="text" name="name" size="30" />
					</td>
				</tr>
				<tr id="editrow" style="display:none">
					<td colspan="2">';
	$GLOBALS['editor_was_set_up']=false;
	$params = array() ;
	/* name must be details !!! if name = data then nothing is displayed */
	$params['name'] = 'details';
	$params['width'] = "800";
	$params['height'] = "300";
	$params['body'] = "";
	$params['group'] = $group_id;
	plugin_hook("text_editor",$params);
	if (!$GLOBALS['editor_was_set_up']) {
		    echo '<textarea name="details" rows="5" cols="80"></textarea>';
}
	unset($GLOBALS['editor_was_set_up']);
	echo '
					</td>
				</tr>';
	if ($dirid) {
		echo '<input type="hidden" name="doc_group" value="'.$dirid.'">';
	} else {
		echo '
				<tr>
					<td>
						<strong>'. _('Directory that document belongs in').'</strong>
                	</td><td>';
		$dgh->showSelectNestedGroups($dgf->getNested(), 'doc_group', false, $dirid);
		echo '
					</td>
				</tr>';
	}
	echo '  </table>';
	echo utils_requiredField() . ' '. _('Mandatory field');
	echo '  <div class="docmanSubmitDiv">
		    	<input type="submit" name="submit" value="'. _('Submit Information').' " />
        	</div>
		</form>';
}

echo '</div>';
?>
