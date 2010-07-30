<?php

/**
 * FusionForge Documentation Manager
 *
 * Copyright 2000, Quentin Cregan/Sourceforge
 * Copyright 2002-2003, Tim Perdue/GForge, LLC
 * Copyright 2010, Franck Villaume
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

foreach ($nested_docs[$dirid] as $d) {
	$dgf = new DocumentGroupFactory($g);
	if ($dgf->isError())
		exit_error('Error',$dgf->getErrorMessage());

	$dgh = new DocumentGroupHTML($g);
	if ($dgh->isError())
		exit_error('Error',$dgh->getErrorMessage());

?>
<script language="javascript">
	function doItEditData<?php echo $d->getID(); ?>() {
		document.getElementById('editdata<?php echo $d->getID(); ?>').submit();
		document.getElementById('submiteditdata<?php echo $d->getID(); ?>').disabled = true;
	}
</script>
<div id="editfile<?php echo $d->getID(); ?>" style="display:none">
<p>
<?php echo _("<strong>Document Title</strong>:  Refers to the relatively brief title of the document (e.g. How to use the download server)<br /><strong>Description:</strong> A brief description to be placed just under the title.") ?>
</p>
<?php
	if ($g->useDocmanSearch())
		echo '<p>'. _('Both fields are used by document search engine.'). '</p>';
?>

	<form id="editdata<?php echo $d->getID(); ?>" name="editdata<?php echo $d->getID(); ?>" action="?group_id=<?php echo $group_id; ?>&action=editfile&dirid=<?php echo $dirid; ?>" method="post" enctype="multipart/form-data">

<table border="0">
	<tr>
		<td>
			<strong><?php echo _('Document Title') ?>: </strong><?php echo utils_requiredField(); ?> <?php printf(_('(at least %1$s characters)'), 5) ?><br />
			<input type="text" name="title" size="40" maxlength="255" value="<?php echo $d->getName(); ?>" />
			<br />
		</td>
	</tr>

    <tr>
        <td>
        <strong><?php echo _('Description') ?></strong><?php echo utils_requiredField(); ?> <?php printf(_('(at least %1$s characters)'), 10) ?><br />
        <input type="text" name="description" size="50" maxlength="255" value="<?php echo $d->getDescription(); ?>" />
        <br /></td>
    </tr>

    <tr>
        <td>
        <strong><?php echo _('File')?></strong><?php echo utils_requiredField(); ?><br />
        <?php if ($d->isURL()) {
            echo '<a href="'.inputSpecialchars($d->getFileName()).'">[View File URL]</a>';
        } else { ?>
        <a target="_blank" href="../view.php/<?php echo $group_id.'/'.$d->getID().'/'.urlencode($d->getFileName()) ?>"><?php echo $d->getName(); ?></a>
        <?php } ?>
        </td>
    </tr>

<?php

    if ((!$d->isURL()) && ($d->isText())) {
        echo '<tr>
	                <td>
		                ';
	
		echo _('Edit the contents to your desire or leave them as they are to remain unmodified.');
		echo '<textarea name="data" rows="15" cols="100" wrap="soft">'. $d->getFileData()  .'</textarea><br />';
		echo '<input type="hidden" name="filetype" value="text/plain">';
		echo '</td>
		            </tr>';
	}

?>

    <tr>
        <td>
        <strong><?php echo _('Group that document belongs in') ?></strong><br />
        <?php
				$dgh->showSelectNestedGroups($dgf->getNested(), 'doc_group', false, $d->getDocGroupID());

	     ?></td>
    </tr>

    <tr>
        <td>
        <br /><strong><?php echo _('State') ?>:</strong><br />
        <?php
		     doc_get_state_box($d->getStateID());
        ?></td>
    </tr>
    <tr>
        <td>
        <?php if ($d->isURL()) { ?>
        <strong><?php echo _('Specify an outside URL where the file will be referenced') ?> :</strong><?php echo utils_requiredField(); ?><br />
        <input type="text" name="file_url" size="50" value="<?php echo $d->getFileName() ?>" />
        <?php } else { ?>
        <strong><?php echo _('OPTIONAL: Upload new file') ?></strong><br />
        <input type="file" name="uploaded_data" size="30" /><br/><br />
            <?php
            	if (forge_get_config('use_ftp_uploads')) {
                	echo '<strong>' ;
                	printf(_('OR choose one form FTP %1$s.'), forge_get_config('ftp_upload_host'));
                	echo '</strong><br />' ;
                	$ftp_files_arr=array_merge($arr,ls($upload_dir,true));
                	echo html_build_select_box_from_arrays($ftp_files_arr,$ftp_files_arr,'ftp_filename','');
                	echo '<br /><br />';
            	}
			}
	        ?>
        </td>
    </tr>
    </table>

    <input type="hidden" name="docid" value="<?php echo $d->getID(); ?>" />
    <input type="button" id="submiteditdata<?php echo $d->getID(); ?>" value="<?php echo _('Submit Edit') ?>" onclick="javascript:doItEditData<?php echo $d->getID(); ?>()" /><br /><br />
    </form>
</div>

<?php
}
?>
