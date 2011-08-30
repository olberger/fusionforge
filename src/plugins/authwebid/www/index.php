<?php

/** External authentication via WebID for FusionForge
 * Copyright 2011, Roland Mas
 * Copyright 2011, Olivier Berger & Institut Telecom
 *
 * This program was developped in the frame of the COCLICO project
 * (http://www.coclico-project.org/) with financial support of the Paris
 * Region council.
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

require_once ('../../../www/env.inc.php');
require_once $gfcommon.'include/pre.php';

// from lightwebid (http://code.google.com/p/lightwebid/)
require_once 'webid.php';

session_require_login();

// get global users vars
$u =& user_get_object(user_getid());
if (!$u || !is_object($u)) {
	exit_error(_('Could Not Get User'));
} elseif ($u->isError()) {
	exit_error($u->getErrorMessage(),'my');
}

$webid_identity = htmlspecialchars(trim(getStringFromRequest('webid_identity', 'http://')));

try {

	// initialize the WebID lib handler which will read the posted args
//TODO	$plugin->webid = new Lightwebid;
	// check the 'webid_mode' that may be set on returning from webid provider
	if($plugin->webid->mode) {

    	// or we are called back by the WebID provider
    	if($plugin->webid->mode == 'cancel') {
        	$warning_msg .= _('User has canceled authentication. Identity not added.');
    	} else {

	    	// Authentication should have been attempted by WebID provider
    		if ($plugin->webid->validate()) {
    			// If user successfully logged in to WebID provider
    			$res = db_query_params('INSERT INTO plugin_authwebid_user_identities (user_id, webid_identity) VALUES ($1,$2)',
					array ($u->getID(),
					$plugin->webid->identity)) ;
				if (!$res || db_affected_rows($res) < 1) {
					$error_msg = sprintf(_('Cannot insert new identity: %s'),
						     db_error());
				} else {
					$feedback = _('Identity successfully added');
					$webid_identity = 'http://';
				}
    		}
    	}
	}
} catch(ErrorException $e) {
    $error_msg = 'webid error: '. $e->getMessage();
    //exit(0);
}

// called to add a new identity
if (getStringFromRequest('addidentity') != '') {
	if ($webid_identity == '' || $webid_identity == 'http://') {
		$error_msg = _('ERROR: Missing URL for the new identity');
	} else if (!util_check_url($webid_identity)) {
		$error_msg = _('ERROR: Malformed URL (only http, https and ftp allowed)');
	} else {
		$res = db_query_params('SELECT webid_identity FROM plugin_authwebid_user_identities WHERE webid_identity =$1',
					array($webid_identity));
		if ($res && db_numrows($res) > 0) {
			$error_msg = _('ERROR: identity already used by a forge user.');
		} else {

			// TODO : redirect and check that the identity is authorized for the user
			try {

				// initialize the WebID lib handler which will read the posted args
				//TODO $plugin->webid = new Lightwebid;
				// check the 'webid_mode' that may be set on returning from WebID provider

            	$plugin->webid->identity = htmlspecialchars_decode($webid_identity);
            	session_redirect_external($plugin->webid->authUrl());

        	} catch(ErrorException $e) {
    			$error_msg = 'webid error: '. $e->getMessage();
    			//exit(0);
			}
		}
	}
} elseif (getStringFromRequest('delete') != '') {
	$webid_identity = urldecode(htmlspecialchars_decode($webid_identity));
	echo 'delete ';
	print_r($webid_identity);
	$res = db_query_params('DELETE FROM plugin_authwebid_user_identities WHERE user_id=$1 AND webid_identity=$2',
				array($u->getID(), $webid_identity));
	if (!$res || db_affected_rows($res) < 1) {
		$error_msg = sprintf(_('Cannot delete identity: %s'), db_error());
	}
	else {
		$feedback = _('Identity successfully deleted');
		$webid_identity = 'http://';
	}
}

$title = sprintf(_('Manage WebID identities for user %1$s'), $u->getUnixName());
site_user_header(array('title'=>$title));

echo $HTML->boxTop(_('My WebID identities'));

?>
<h2><?php echo _('Add new identity'); ?></h2>

<p><?php echo _('You can add your own WebID identities in the form below.') ?></p>

<form name="new_identity" action="<?php echo util_make_uri ('/plugins/authwebid/'); ?>" method="post">
<fieldset>
<legend><?php echo _('Add new identity'); ?></legend>
<p>
<input type="hidden" name="user_id" value="<?php echo $u->getID() ?>" />
<input type="hidden" name="addidentity" value="1" />
<strong><?php echo _('WebID identity URL:') ?></strong><?php echo utils_requiredField(); ?>
<br />
<input type="text" size="150" name="webid_identity" value="<?php echo $webid_identity ?>" /><br />
</p>
<p>
<input type="submit" value="<?php echo _('Add identity') ?>" />
</p>
</fieldset>
</form>
<?php

echo $HTML->listTableTop(array(_('Identity'), ''));

$res = db_query_params('SELECT webid_identity FROM plugin_authwebid_user_identities WHERE user_id =$1',
							    array($u->getID()));
if($res) {
	$i = 0;

	while ($row = db_fetch_array($res)) {
		$webid_identity = 	$row['webid_identity'];

		echo '<tr '.$HTML->boxGetAltRowStyle($i).'>';
		echo '<td>'. $webid_identity .'</td>';
		echo '<td><a href="'.util_make_uri ('/plugins/authwebid/').'?webid_identity='. urlencode($webid_identity) .'&delete=1">delete</a></td>';
		echo '</tr>';
		$i++;
	}
}

echo $HTML->listTableBottom();

echo $HTML->boxBottom();

site_user_footer(array());

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
