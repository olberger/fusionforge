<?php
/**
 * Reporting System
 *
 * Copyright 2003-2004 (c) GForge LLC
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once('../env.inc.php');
require_once $gfcommon.'include/pre.php';
require_once $gfcommon.'reporting/report_utils.php';
require_once $gfcommon.'reporting/Report.class.php';

session_require_global_perm ('forge_stats', 'read') ;

$report=new Report();
if ($report->isError()) {
	exit_error($report->getErrorMessage());
}

$g_id = getIntFromRequest('g_id');
$area = getStringFromRequest('area');
$SPAN = getIntFromRequest('SPAN', 1);
$start = getIntFromRequest('start');
$end = getIntFromRequest('end');

if (!$start) {
	$z =& $report->getMonthStartArr();
	$start = $z[0];
}
if (!$end || $end <= $start) {
	$z =& $report->getMonthStartArr();
	$end = $z[count($z)-1];
}

report_header(_('Project Activity'));

?>
<h2><?php echo _('Project Activity'); ?></h2>

<form action="<?php echo getStringFromServer('PHP_SELF'); ?>" method="get">
<input type="hidden" name="sw" value="<?php echo $sw; ?>" />
<table><tr>
<td><strong><?php echo _('Project'); ?>:</strong><br /><?php echo report_group_box('g_id',$g_id); ?></td>
<td><strong><?php echo _('Areas'); ?>:</strong><br /><?php echo report_area_box('area',$area); ?></td>
<td><strong><?php echo _('Type'); ?>:</strong><br /><?php echo report_span_box('SPAN',$SPAN); ?></td>
<td><strong><?php echo _('Start'); ?>:</strong><br /><?php echo report_months_box($report, 'start', $start); ?></td>
<td><strong><?php echo _('End'); ?>:</strong><br /><?php echo report_months_box($report, 'end', $end); ?></td>
<td><input type="submit" name="submit" value="<?php echo _('Refresh'); ?>" /></td>
</tr></table>
</form>
<?php if ($g_id) { ?>
	<p>
	<img src="projectact_graph.php?<?php echo "SPAN=$SPAN&amp;start=$start&amp;end=$end&g_id=$g_id&amp;area=$area"; ?>" width="640" height="480" alt="" />
	</p>
	<?php

}

report_footer();

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
