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
			// $tabrole = getStringFromPost('roles');
		        // $list_roles = implode(',',$tabrole);
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
				getStringFromPost('description'),
				getArrayFromRequest('roles')
			)) {
				form_release_key(getStringFromRequest("form_key"));
				// exit_error(_('Error'), $mailingListSympa->getErrorMessage());
				$feedback .=  $mailingListSympa->getErrorMessage();
			} else {
				$feedback .= _('List Added');
			}
		//
		//	Change status
		//
/*
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

*/
               } elseif ((getStringFromPost('delete_list') == 'y') && (getStringFromPost('confirm') == 'O')) {
                        $mailingListSympa = new MailingListSympa($Group);
                       if(!$mailingListSympa || !is_object($mailingListSympa)) {
                                form_release_key(getStringFromRequest("form_key"));
                                exit_error(_('Error'), _('Error getting the list'));
                        } elseif($mailingListSympa->isError()) {
                                form_release_key(getStringFromRequest("form_key"));
                                exit_error(_('Error'), $mailingListSympa->getErrorMessage());
                        }
                        if(!$mailingListSympa->delete(
                                getStringFromPost('list_name')
                        )) {
                                form_release_key(getStringFromRequest("form_key"));
                                // exit_error(_('Error'), $mailingListSympa->getErrorMessage());
                                $feedback .=  $mailingListSympa->getErrorMessage();
                        } else {
                                $feedback .= _('List deleted');
                        }
 
	      }
	}

//
//	Form to add list
//

	if ( (getIntFromGet('add_list')) || ((getStringFromRequest('add_list') && (getStringFromRequest('add_list')=='y') ) && (isset($feedback)) && (trim($feedback)!='') && ($feedback != _('List Added')) && ($feedback != _('List deleted'))  ) ) {
	
	mailSympa_header(array(
			'title' => _('Add a Mailing List')));
		/*
		printf(_('<p>Lists are named in this manner:<br /><strong>projectname-listname@%1$s</strong></p><p>It will take <span class="important">5-10 minutes</span> for your list to be created.</p>'), $GLOBALS['sys_lists_host']);
		*/
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
//
//	Form to add list
//
		?>

<SCRIPT type="text/JavaScript">
	
function role(nom_liste){
	var tab_role = document.forms["add_list"].elements["roles[]"];
	var nom_role='';
	var j=0;
 	for (var i=0;i<tab_role.length;i=i+1) {
  	  if (tab_role[i].checked) {
		 nom_role=tab_role[i].value;
		 nom_role=nom_role.replace(/\d/g,'');
		  j=j+1;	
	}
	if (j>1) {
	document.forms["add_list"].elements["list_name"].value=nom_liste;		
	return false;	
	}
	}
	document.forms["add_list"].elements["list_name"].value=nom_liste+'-'+nom_role;
}
</script>

		<form name="add_list" method="post" action="<?php echo getStringFromServer('PHP_SELF'); ?>?group_id=<?php echo $group_id ?>">
			<input type="hidden" name="post_changes" value="y" />
			<input type="hidden" name="add_list" value="y" />
			<input type="hidden" name="form_key" value="<?php echo form_generate_key();?>" />
			
			<!-- Ajout de la prise en compte des roles à associer à la liste -->
                     <p><strong><?php echo _('Role of project members associated with this list'); ?>(*) : </strong><br />
		<?php
 	               $result = db_query_params('SELECT role_id,role_name FROM role WHERE group_id=$1  ORDER BY role_name',array($group_id));
          		if (!$result) {
                        	$this->setError(sprintf(_('Error Getting %1$s'), _('Error Getting %1$s')).db_error());
                	} else {
                        while ($row = db_fetch_array($result)) {
			?>
                              <input type="checkbox" name="roles[]" value="<?php echo($row['role_id'].strtolower(str_replace(' ','',$row['role_name']))) ?>" onclick="javascript:role('<?php echo $Group->getUnixName(); ?>')">&nbsp;<?php echo($row['role_name']) ?><br />
		<?php	
				}
			}
		?>
</p>
                        <p><strong><?php echo _('Mailing List Name '); ?>(*) :</strong><br />

                       <strong><input type="text" name="list_name" value="<?php echo $Group->getUnixName(); ?>" size="20" maxlength="12" />@<?php echo $GLOBALS['sys_mail_host']; ?></strong><br /></p>
                        <p>
                        <strong><?php echo _('Description '); ?>(*) : </strong><br />
                        <input type="text" name="description" value="" size="40" maxlength="80" /><br /></p>
                        <p>
			<br/>
			<input type="submit" name="submit" value="<?php echo _('Add This List'); ?>" /></p>
		</form>
		<br/><br/>
		<?php
		 echo(" (*) ". _('All this informations should be filled')); 
		mailSympa_footer(array());

//
//	Form to modify list
//
/*
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
*/
	} 
// Suppression de liste
 elseif ( getIntFromGet('delete_list')==1  ) {
  
      mailSympa_header(array('title' => _('Delete a Mailing List')));

?>

         <form name="delete_list" method="post" action="<?php echo getStringFromServer('PHP_SELF'); ?>?group_id=<?php echo $group_id ?>">
                <input type="hidden" name="post_changes" value="y" />
                <input type="hidden" name="delete_list" value="y" />
                 <input type="hidden" name="form_key" value="<?php echo form_generate_key();?>" />
		<input type="hidden" name="list_name" value="<?php echo(getStringFromGet('list_name')); ?>" />
                <p>
<?php echo _('Confirm deletion of list ').' '.getStringFromGet('list_name'). ' ?'; ?>
</p>
         
		<input type="radio" name="confirm" value="O" />Oui<br />
		<input type="radio" name="confirm" value="N" checked />Non<br /><br/>
		<input type="submit" name="submit" value="<?php echo _('Permanently Delete'); ?>" />
                </form>
                <br/><br/>
                <?php
                mailSympa_footer(array());
   }


else {
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

echo $HTML->subMenu(array(_('Add Mailing List')),array('/plugins/sympa/admin/?group_id='.$group_id.'&amp;add_list=1'));
// array( '/plugins/sympa/admin/?group_id='.$group_id)

		$mlArray =& $mlFactory->getMailingListsSympa();

		if ($mlFactory->isError()) {
			echo '<p>'._('Error').' '.sprintf(_('Unable to get the list %s'), $Group->getPublicName()) .'</p>';
			echo $mlFactory->getErrorMessage();
			mailSympa_footer(array());
			exit;
		}
		echo '<p>'.sprintf(_('You can administrate lists from here.')).'</p>';

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
				
					$currentListName=$currentList->getName();	
					echo('<td><a href="'.util_make_url("/plugins/sympa/admin/?group_id=$group_id&amp;delete_list=1&amp;list_name=$currentListName").'"> '.html_image('ic/trash.png',"15","15",array("border"=>"0")).'</<a></td>');
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
