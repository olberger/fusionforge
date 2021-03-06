<?php
/**
 * MantisBT plugin
 *
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

/* view version of a dedicated group in MantisBt */

/* main display */
global $HTML;
global $mantisbt;
global $mantisbtConf;
global $username;
global $password;

try {
	/* do not recreate $clientSOAP object if already created by other pages */
	if (!isset($clientSOAP))
		$clientSOAP = new SoapClient($mantisbtConf['url']."/api/soap/mantisconnect.php?wsdl", array('trace'=>true, 'exceptions'=>true));

	$listVersions = $clientSOAP->__soapCall('mc_project_get_versions', array("username" => $username, "password" => $password, "project_id" => $mantisbtConf['id_mantisbt']));
} catch  (SoapFault $soapFault) {
	echo '<div class="warning" >'. _('Technical error occurs during data retrieving:'). ' ' .$soapFault->faultstring.'</div>';
	$errorPage = true;
}

if (!isset($errorPage)){
	echo $HTML->boxTop(_('Manage versions'));
	if (sizeof($listVersions)) {
		echo '<table>';
		echo	'<tr>';
		echo		'<td>'._('Version').'</td>';
		echo		'<td>'._('Description').'</td>';
		echo		'<td>'._('Target Date').'</td>';
		echo		'<td>'._('Type').'</td>';
		echo 		'<td>'._('Action').'</td>';
		echo	'</tr>';
		$i = 1;
		foreach ($listVersions as $key => $version){
			echo '<tr '.$HTML->boxGetAltRowStyle($i).'">';
			echo '<td>'.$version->name.'</td>';
			(isset($version->description))? $description_value = $version->description : $description_value = '';
			echo '<td>'.$description_value.'</td>';
			echo '<td>'.strftime("%d/%m/%Y",strtotime($version->date_order)).'</td>';
			/* est-ce une version release ? */
			if ( $version->released ) {
				echo '<td>Release</td>';
			/* juste une milestone alors */
			} else {
				echo '<td>Milestone</td>';
			}
			echo '<td>';
			echo '<a href="?type=admin&group_id='.$group_id.'&pluginname='.$mantisbt->name.'&view=editVersion&idVersion='.$version->id.'" ">'._('Update').'</a>';
			echo '</td></tr>';
			$i++;
		}
		echo '</table>';
	} else {
		echo '<p class="warning">'._('No versions').'</p>';
	}
	echo $HTML->boxBottom();
}
?>
