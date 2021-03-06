<?php
/**
 * MantisBT plugin
 *
 * Copyright 2011, Franck Villaume - Capgemini
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

global $HTML;
global $mantisbt;
global $mantisbtConf;

echo $HTML->boxTop(_('Manage configuration'));
echo '<form method="POST" Action="?type=admin&group_id='.$group_id.'&pluginname='.$mantisbt->name.'&action=updateConf">';
echo '<table>';
echo '<tr><td><label id="mantisbtinit-url" ';
if ($use_tooltips)
	echo 'title="'._('Specify the Full URL of the MantisBT Web Server.').'"';
echo ' >URL</label></td><td><input type="text" size="50" maxlength="255" name="url" value="'.$mantisbtConf['url'].'" /></td></tr>';
echo '<tr><td><label id="mantisbtinit-user" ';
if ($use_tooltips)
	echo 'title="'._('Specify the user with admin right to be used thru SOAP API.').'"';
echo ' >SOAP User</label></td><td><input type="text" size="50" maxlength="255" name="soap_user" value="'.$mantisbtConf['soap_user'].'" /></td></tr>';
echo '<tr><td><label id="mantisbtinit-password" ';
if ($use_tooltips)
	echo 'title="'._('Specify the password of this user.').'"';
echo ' >SOAP Password</label></td><td><input type="text" size="50" maxlength="255" name="soap_password" value="'.$mantisbtConf['soap_password'].'" /></td></tr>';
echo '<tr><td><label id="mantisbtinit-syncusers" ';
if ($use_tooltips)
	echo 'title="'._('Do you want to sync FusionForge -> MantisBT users ?').'"';
echo ' >Sync Users</label></td><td><input disabled="disabled" type="checkbox" name="sync_user" /></td></tr>';
echo '<tr><td><label id="mantisbtinit-syncroles" ';
if ($use_tooltips)
	echo 'title="'._('Do you want to sync FusionForge -> MantisBT roles ?').'"';
echo ' >Sync Roles</label></td><td><input disabled="disabled" type="checkbox" name="sync_roles" /></td></tr>';
echo '</table>';
echo '<input type="submit" value="'._('Update').'" />';
echo '</form>';
echo $HTML->boxBottom();
?>
