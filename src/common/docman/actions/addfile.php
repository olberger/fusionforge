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

/* please do not add require here : use www/docman/index.php to add require */
/* global variables used */
global $g; //group object
global $group_id; // id of group
global $sys_engine_path; // path to the docman search engine

$upload_dir = forge_get_config('ftp_upload_dir') . "/" . $g->getUnixName();

$doc_group = getIntFromRequest('doc_group');
$title = getStringFromRequest('title');
$description = getStringFromRequest('description');
$file_url = getStringFromRequest('file_url');
//$ftp_filename = getStringFromRequest('ftp_filename');
$uploaded_data = getUploadedFile('uploaded_data');
$type = getStringFromRequest('type');
$name = getStringFromRequest('name');

if (!$doc_group || $doc_group == 100) {
	//cannot add a doc unless an appropriate group is provided		
	exit_error(_('Error'),_('No valid Document Group was selected.'));
}
	
if (!$title || !$description || (!$uploaded_data && !$file_url && (!$editor && !$name ) )) {		
	exit_missing_param();
}

if (!isset($sys_engine_path))
	$sys_engine_path = dirname(__FILE__).'/../engine/';

$d = new Document($g, false, false,$sys_engine_path);
if (!$d || !is_object($d)) {		
	exit_error(_('Error'),_('Error getting blank document.'));
} elseif ($d->isError()) {	
	exit_error(_('Error'),$d->getErrorMessage());
}
	
switch ($type) {
	case 'editor' : {
		$data = getStringFromRequest('data');
		$uploaded_data_name = $name;
		$sanitizer = new TextSanitizer();
		$data = $sanitizer->SanitizeHtml($data);
		if (strlen($data)<1) {
			exit_error(_('Error'),_('Error getting blank document.'));
		}
		$uploaded_data_type='text/html';
		break;
	}
	case 'pasteurl' : {
		$data = '';
		$uploaded_data_name=$file_url;
		$uploaded_data_type='URL';		
		break;
	}
	case 'httpupload' : {
		if (!is_uploaded_file($uploaded_data['tmp_name'])) {			
			exit_error(_('Error'),_('Invalid file name.'));
		}
		$data = fread(fopen($uploaded_data['tmp_name'], 'r'), $uploaded_data['size']);
		$file_url='';
		$uploaded_data_name=$uploaded_data['name'];
		$uploaded_data_type=$uploaded_data['type'];
		break;
	}
	/*
	case 'ftpupload' : {	
		$uploaded_data_name=$upload_dir.'/'.$ftp_filename;
		$data = fread(fopen($uploaded_data_name, 'r'), filesize($uploaded_data_name));
	}
	*/
}

if (!$d->create($uploaded_data_name,$uploaded_data_type,$data,$doc_group,$title,$description)) {
		exit_error(_('Error'),$d->getErrorMessage());
} else {		
	if ($type == 'editor') {
		//release the cookie for the document contents (should expire at the end of the session anyway)
		setcookie ("gforgecurrentdocdata", "", time() - 3600);
	}
	// check if the user is docman's admin
	if (forge_check_perm ('docman', $group_id, 'approve')) {
		$feedback= _('Document submitted successfully');
		Header('Location: '.util_make_url('/docman/?group_id='.$group_id.'&view=listfile&dirid='.$doc_group.'&feedback='.urlencode($feedback)));
		exit;
    } else {
		$feedback= _('Document submitted successfully : pending state (need validation)');
		Header('Location: '.util_make_url('/docman/?group_id='.$group_id.'&feedback='.urlencode($feedback)));
		exit;
	}
}

?>
