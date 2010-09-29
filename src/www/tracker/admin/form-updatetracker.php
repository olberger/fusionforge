<?php
/*
 * Tracker Facility
 *
 * Copyright 2010 (c) FusionForge Team
 * http://fusionforge.org
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

if (!defined('BASE')) require('illegal_access.inc.php');
//
//	FORM TO UPDATE ARTIFACT TYPES
//
$ath->adminHeader(array ('title'=>sprintf(_('Modify A Group In %s'),$ath->getName())));

		?>
		<form action="<?php echo getStringFromServer('PHP_SELF').'?group_id='.$group_id.'&amp;atid='.$ath->getID(); ?>" method="post">
		<p>
		<input type="hidden" name="update_type" value="y" />
		<?php echo _('<strong>Name:</strong> (examples: meeting minutes, test results, RFP Docs)') ?><br />
		<?php if ($ath->getDataType()) {
			echo $ath->getName();
		} else { 
			?>
			<input type="text" name="name" value="<?php echo $ath->getName(); ?>" />
			<?php 
		} 
		?>
		</p>
		<p>
		<strong><?php echo _('Description') ?>:</strong><br />
		<?php if ($ath->getDataType()) {
			echo $ath->getDescription();
		} else {
			?>
			<input type="text" name="description" value="<?php echo $ath->getDescription(); ?>" size="50" />
			<?php 
		} 
		?>
		</p>
		<p>
		<strong><?php echo _('Send email on new submission to address') ?>:</strong><br />
		<input type="text" name="email_address" value="<?php echo $ath->getEmailAddress(); ?>" /></p>
		<p>
		<input type="checkbox" name="email_all" value="1" <?php echo (($ath->emailAll())?'checked="checked"':''); ?> /> <strong><?php echo _('Send email on all changes') ?></strong><br /></p>
		<p>
		<strong><?php echo _('Days till considered overdue') ?>:</strong><br />
		<input type="text" name="due_period" value="<?php echo ($ath->getDuePeriod() / 86400); ?>" /></p>
		<p> 
		<strong><?php echo _('Days till pending tracker items time out') ?>:</strong><br />
		<input type="text" name="status_timeout"  value="<?php echo($ath->getStatusTimeout() / 86400); ?>" /></p>
		<p>
		<strong><?php echo _('Free form text for the "submit new item" page') ?>:</strong><br />
		<textarea name="submit_instructions" rows="10" cols="55"><?php echo $ath->getSubmitInstructions(); ?></textarea></p>
		<p>
		<strong><?php echo _('Free form text for the "browse items" page') ?>:</strong><br />
		<textarea name="browse_instructions" rows="10" cols="55"><?php echo $ath->getBrowseInstructions(); ?></textarea></p>
		<p>
		<input type="submit" name="post_changes" value="<?php echo _('Submit') ?>" /></p>
		</form>
		<?php

		$ath->footer(array());

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
