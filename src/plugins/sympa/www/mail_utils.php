<?php

function mailSympa_header($params) {
	global $HTML, $group_id;

	if ($group_id) {
		//required for site_project_header
		$params['group'] = $group_id;
		$params['toptab'] = 'sympa';

		$project =& group_get_object($group_id);

		if ($project && is_object($project)) {
			if (!$project->usesPlugin('sympa')) {
				exit_error(_('Error'), _('This Project Has Turned Off The Mailing Lists'));
			}
		}


		site_project_header($params);
		if (session_loggedin()) {
			$perm =& $project->getPermission(session_get_user());
			if ($perm && is_object($perm) && !$perm->isError() && $perm->isAdmin()) {
				echo $HTML->subMenu(
					array(
						_('Admin')
					),
					array(
						'/plugins/sympa/admin/?group_id='.$group_id
					)
				);
			}
		}
	} else {
		exit_no_group();
	}
}

function mailSympa_footer($params) {
	site_project_footer($params);
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
