<?php
/**
 *
 */

require_once('../../../www/env.inc.php');
require_once $gfwww.'include/pre.php';
require_once './mail_utils.php';

require_once '../common/MailingListSympa.class.php';
require_once '../common/MailingListFactorySympa.class.php';

$group_id = getIntFromGet('group_id');

if ($group_id) {
	$Group =& group_get_object($group_id);
	if (!$Group || !is_object($Group)) {
		exit_error(_('Error'), 'Could Not Get Group');
	} elseif ($Group->isError()) {
		exit_no_group();
	}

	$mlFactorySympa = new MailingListFactorySympa($Group);

	if (!$mlFactorySympa || !is_object($mlFactorySympa)) {
		exit_error(_('Error'), 'Could Not Get MailingListFactory');
	} elseif ($mlFactorySympa->isError()) {
		exit_error(_('Error'), $mlFactorySympa->getErrorMessage());
	}
	
	mailSympa_header(array(
		'title' => sprintf(_('Mailing Lists for %1$s'), $Group->getPublicName())
	));
       
	$res = db_query("SELECT * FROM lists "
			. "WHERE group_id='".$group_id."'"
			. "ORDER BY list_name;");

	if (!$res) {
	echo '<h1>'._('Error').' '.sprintf(_('Unable to get the list %s'), $Group->getPublicName()) .'</h1>';
	echo $mlFactorySympa->getErrorMessage();
	}


	if(db_numrows($res) < 1) {
	 	echo '<p>'.sprintf(_('No Lists found for %1$s'), $Group->getPublicName()) .'</p>';
		echo '<p>'._('Project administrators use the admin link to request mailing lists.').'</p>';

	} else {
	
	echo _('<p>Mailing lists provided via <a href="http://www.sympa.org/"> Sympa</a>.');
	echo _('<p>Choose a list to browse, search, and post messages.</p>');
	$tableHeaders = array(
		_('Mailing list'),
		_('Description'),
	);
	echo $HTML->listTableTop($tableHeaders);
	

	  for($i = 0; $i < db_numrows($res); $i++) {
	
	    echo '<tr '. $HTML->boxGetAltRowStyle($i) .'>';

	    $list_name = db_result($res, $i, 'list_name');
	    $list_url = db_result($res, $i, 'list_url');
	    $url = "<a href=\"$list_url\">$list_name</a>";

	    if(!(strstr($list_url, 'http://')||strstr($list_url, 'https://'))) {
	      $url = "<a href=\"mailto:$list_url\">$list_name</a>";
	    }
	    $list_description = db_result($res, $i, 'list_description');
	    $list_archives = db_result($res, $i, 'list_archives');
	    $archives = '';
	    if(!$list_archives && (strstr($list_url, 'http://') || strstr($list_url, 'https://'))) {
	      $list_archives = str_replace('/info/', '/arc/', $list_url);
	    }
	    if($list_archives) {
	      $archives = " [<a href=\"$list_archives\">Archives</a>]";
	    }
	    echo '<td width="20%">'.
	      '<strong><a href="'.$list_url.'">' .
	      sprintf($list_name).'</a></strong>'.$archives.'</td>'.
	      '<td>'.htmlspecialchars($list_description). '</td>';
	    
	  }

	}
	echo '</tr>';
	echo $HTML->listTableBottom();
	mailSympa_footer(array());

} else {

	exit_no_group();

}


?>
