<?php

require_once('../../../../www/env.inc.php');
require_once $gfwww.'include/pre.php';
require_once '../mail_utils.php';

require_once '../../common/MailingListSympa.class.php';
require_once '../../common/MailingListFactorySympa.class.php';

$group_id = getIntFromRequest('group_id');

$feedback = '';

if ($group_id) {
	$Group =& group_get_object($group_id);
	if (!$Group || !is_object($Group) || $Group->isError()) {
		exit_no_group();
	}
	
	$perm =& $Group->getPermission(session_get_user());
	if (!$perm || !is_object($perm) || $perm->isError() || !$perm->isAdmin()) {
		exit_permission_denied();
	}
	
//
//	Post Changes to database
//
	if (getStringFromRequest('post_changes') == 'y') {
		//
		//	Add list
		//
		if (getStringFromRequest('add_list') == 'y') {
			$mailingListSympa = new MailingListSympa($Group);
			/*
			if (!form_key_is_valid(getStringFromRequest('form_key'))) {
				exit_form_double_submit();
			}
			*/
			if(!$mailingListSympa || !is_object($mailingListSympa)) {
				form_release_key(getStringFromRequest("form_key"));
				exit_error(_('Error'), _('Error getting the list'));
			} elseif($mailingListSympa->isError()) {
				form_release_key(getStringFromRequest("form_key"));
				exit_error(_('Error'), $mailingListSympa->getErrorMessage());
			}
			
			if(!$mailingListSympa->create(
				getStringFromPost('list_name'),
				getStringFromPost('description')
			)) {
		       
				form_release_key(getStringFromRequest("form_key"));
				exit_error(_('Error'), "$mailingListSympa->getErrorMessage()");
			} else {

				$feedback .= _('List Added');
				//	  print('???what');
			}
		//
		//	Change status
		//
		} elseif (getStringFromPost('change_status') == 'y') {
			$mailingListSympa = new MailingListSympa($Group, getIntFromGet('list_id'));
			
			if(!$mailingListSympa || !is_object($mailingListSympa)) {
				exit_error(_('Error'), _('Error getting the list'));
			} elseif($mailingListSympa->isError()) {
				exit_error(_('Error'), $mailingListSympa->getErrorMessage());
			}
			
			if(!$mailingListSympa->update(
				unInputSpecialChars(getStringFromPost('description')))) {
				exit_error(_('Error'), $mailingListSympa->getErrorMessage());
			} else {
				$feedback .= _('List updated');
			}
		}

	}

//
//	Form to add list
//
	if(getIntFromGet('add_list')) {
		mailSympa_header(array(
			'title' => _('Add a Mailing List')));
		printf(_('<p>Lists are named in this manner:<br /><strong>projectname-listname@%1$s</strong></p><p>It will take <span class="important">5-10 minutes</span> for your list to be created.</p>'), $GLOBALS['sys_lists_host']);
		
		$mlFactory = new MailingListFactorySympa($Group);
		if (!$mlFactory || !is_object($mlFactory) || $mlFactory->isError()) {
			exit_error(_('Error'), $mlFactory->getErrorMessage());
		}
		
		$mlArray =& $mlFactory->getMailingListsSympa();

		if ($mlFactory->isError()) {
			echo '<h1>'._('Error').' '._('Unable to get the lists') .'</h1>';
			echo $mlFactory->getErrorMessage();
			mailSympa_footer(array());
			exit;
		}
		
		$tableHeaders = array(
			_('Existing mailing lists')
		);
//
//	Show lists
//
		$mlCount = count($mlArray);
		if($mlCount > 0) {
			echo $HTML->listTableTop($tableHeaders);
			for ($j = 0; $j < $mlCount; $j++) {
				$currentList =& $mlArray[$j];
				if ($currentList->isError()) {
					echo '<tr '. $HTML->boxGetAltRowStyle($j) . '><td>';
					echo $currentList->getErrorMessage();
					echo '</td></tr>';
				} else {
					echo '<tr '. $HTML->boxGetAltRowStyle($j) . '><td>'.$currentList->getName().'</td></tr>';
				}
			}
			echo $HTML->listTableBottom();
		}
//
//	Form to add list
//
		?>
		<form method="post" action="<?php echo getStringFromServer('PHP_SELF'); ?>?group_id=<?php echo $group_id ?>">
			<input type="hidden" name="post_changes" value="y" />
			<input type="hidden" name="add_list" value="y" />
			<input type="hidden" name="form_key" value="<?php echo form_generate_key();?>" />
			<p><strong><?php echo _('Mailing List Name:'); ?></strong><br />
			<strong><?php echo $Group->getUnixName(); ?>-<input type="text" name="list_name" value="" size="10" maxlength="12" />@<?php echo $GLOBALS['sys_lists_host']; ?></strong><br /></p>
			<p>
			<strong><?php echo _('Description:'); ?></strong><br />
			<input type="text" name="description" value="" size="40" maxlength="80" /><br /></p>
			<p>
			<input type="submit" name="submit" value="<?php echo _('Add This List'); ?>" /></p>
		</form>
		<?php
		mailSympa_footer(array());

//
//	Form to modify list
//
	} elseif(getIntFromGet('change_status') && getIntFromGet('group_list_id')) {
		$mailingListSympa = new MailingListSympa($Group, getIntFromGet('group_list_id'));
			
		if(!$mailingListSympa || !is_object($mailingListSympa)) {
			exit_error(_('Error'), _('Error getting the list'));
		} elseif($mailingListSympa->isError()) {
			exit_error(_('Error'), $mailingListSympa->getErrorMessage());
		}
   	
		mailSympa_header(array(
			'title' => _('Mail admin')));
		?>
		<h3><?php echo $mailingListSympa->getName(); ?></h3>
		<form method="post" action="<?php echo getStringFromServer('PHP_SELF'); ?>?group_id=<?php echo $group_id; ?>&amp;group_list_id=<?php echo $mailingListSympa->getID(); ?>">
			<input type="hidden" name="post_changes" value="y" />
			<input type="hidden" name="change_status" value="y" />
		
			<p><strong><?php echo _('Description:'); ?></strong><br />
			<input type="text" name="description" value="<?php echo inputSpecialChars($mailingListSympa->getDescription()); ?>" size="40" maxlength="80" /><br /></p>
			<p>
			<input type="submit" name="submit" value="<?php echo _('Update'); ?>" /></p>
		</form>
		<a href="deletelist.php?group_id=<?php echo $group_id; ?>&amp;group_list_id=<?php echo $mailingListSympa->getID(); ?>">[<?php echo _('Permanently Delete List'); ?>]</a>
	<?php
		mailSympa_footer(array());
	} else {
//
//	Show lists
//
		$mlFactory = new MailingListFactorySympa($Group);
		if (!$mlFactory || !is_object($mlFactory) || $mlFactory->isError()) {
			exit_error(_('Error'), $mlFactory->getErrorMessage());
		}

		mailSympa_header(array(
			'title' => _('Mailing List Administration'))
		);

		$mlArray =& $mlFactory->getMailingListsSympa();

		if ($mlFactory->isError()) {
			echo '<p>'._('Error').' '.sprintf(_('Unable to get the list %s'), $Group->getPublicName()) .'</p>';
			echo $mlFactory->getErrorMessage();
			mailSympa_footer(array());
			exit;
		}
		echo '<p>'.sprintf(_('You can administrate lists from here.')).'</p>';
		echo '<ul>
			<li>
				<a href="'.getStringFromServer('PHP_SELF').'?group_id='.$group_id.'&amp;add_list=1">'._('Add Mailing List').'</a>
			</li>
		</ul>';
		$mlCount = count($mlArray);
		if($mlCount > 0) {
			$tableHeaders = array(
				_('Mailing list'),
				''
			);
			echo $HTML->listTableTop($tableHeaders);
			for ($i = 0; $i < $mlCount; $i++) {
				$currentList =& $mlArray[$i];
				if ($currentList->isError()) {
					echo '<tr '. $HTML->boxGetAltRowStyle($i) .'><td colspan="3">';
					echo $currentList->getErrorMessage();
					echo '</td></tr>';
				} else {
					echo '<tr '. $HTML->boxGetAltRowStyle($i) . '><td width="60%">'.
					'<strong>'.$currentList->getName().'</strong><br />'.
					htmlspecialchars($currentList->getDescription()).'</td>'.

					'<td width="20%" style="text-align:center">';

						echo '<a href="'.$currentList->getExternalAdminUrl().'">'._('Administrate').'</a></td>';
					
					echo '</tr>';
				}
			}
			echo $HTML->listTableBottom();
		}
		mailSympa_footer(array());
	}
} else {
	exit_no_group();
}
?>
