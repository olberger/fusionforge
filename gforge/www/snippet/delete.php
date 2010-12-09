<?php
/**
  *
  * SourceForge Code Snippets Repository
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  */


require_once('../env.inc.php');
require_once $gfwww.'include/pre.php';
require_once $gfwww.'snippet/snippet_utils.php';
/*
	By Tim Perdue, 2000/01/10

	Delete items from packages, package versions, and snippet versions
*/

if (session_loggedin()) {
	snippet_header(array('title'=>'Delete Snippets'));
	$type = getStringFromRequest('type');
	$snippet_version_id = getIntFromRequest('snippet_version_id');
	$snippet_package_version_id = getIntFromRequest('snippet_package_version_id');

	if ($type=='frompackage' && $snippet_version_id && $snippet_package_version_id) {
		/*
			Delete an item from a package
		*/

		//Check to see if they are the creator of this package_version
		$result=db_query_params("SELECT * FROM snippet_package_version ".
			"WHERE submitted_by=$1 AND ".
			"snippet_package_version_id=$2", array(user_getid(), $snippet_package_version_id));
		if (!$result || db_numrows($result) < 1) {
			echo '<h1>Error - Only the creator of a package version can delete snippets from it.</h1>';
			snippet_footer(array());
			exit;
		} else {

			//Remove the item from the package
			$result=db_query_params ('DELETE FROM snippet_package_item 
WHERE snippet_version_id=$1 
AND snippet_package_version_id=$2',
			array($snippet_version_id,
				$snippet_package_version_id));
			if (!$result || db_affected_rows($result) < 1) {
				echo '<h1>Error - That snippet doesn\'t exist in this package.</h1>';
				snippet_footer(array());
				exit;
			} else {
				echo '<h1>Item Removed From Package</h1>';
				snippet_footer(array());
				exit;
			}
		}

	} else  if ($type=='snippet' && $snippet_version_id) {
		/*
			Delete a snippet version
		*/

		//find this snippet id and make sure the current user created it
		$result=db_query_params("SELECT * FROM snippet_version ".
			"WHERE snippet_version_id=$1 AND submitted_by=$2", array($snippet_version_id, user_getid()));
		if (!$result || db_numrows($result) < 1) {
			echo '<h1>Error - That snippet doesn\'t exist.</h1>';
			snippet_footer(array());
			exit;
		} else {
			$snippet_id=db_result($result,0,'snippet_id');

			//do the delete
			$result=db_query_params("DELETE FROM snippet_version ".
				"WHERE snippet_version_id=$1 AND submitted_by=$2", array($snippet_version_id, user_getid()));

			//see if any versions of this snippet are left
			$result=db_query_params("SELECT * FROM snippet_version WHERE snippet_id=$1", array($snippet_id));
			if (!$result || db_numrows($result) < 1) {
				//since no version of this snippet exist, delete the main snippet entry,
				//even if this person is not the creator of the original snippet
				$result=db_query_params("DELETE FROM snippet WHERE snippet_id=$1",array($snippet_id));
			}

			echo '<h1>Snippet Removed</h1>';
			snippet_footer(array());
			exit;
		}

	} else  if ($type=='package' && $snippet_package_version_id) {
		/*
			Delete a package version

		*/

		//make sure they own this version of the package
		$result=db_query_params("SELECT * FROM snippet_package_version ".
			"WHERE submitted_by=$1 AND ".
			"snippet_package_version_id=$2", array(user_getid(), $snippet_package_version_id));
		if (!$result || db_numrows($result) < 1) {
			//they don't own it or it's not found
			echo '<h1>Error - Only the creator of a package version can delete it.</h1>';
			snippet_footer(array());
			exit;
		} else {
			$snippet_package_id=db_result($result,0,'snippet_package_id');

			//do the version delete
			$result=db_query_params("DELETE FROM snippet_package_version ".
		       		"WHERE submitted_by=$1 AND ".
				"snippet_package_version_id=$2", array(user_getid(), $snippet_package_version_id));

			//delete snippet_package_items
			$result=db_query_params("DELETE FROM snippet_package_item ".
				"WHERE snippet_package_version_id=$1", array($snippet_package_version_id));

			//see if any versions of this package remain
			$result=db_query_params("SELECT * FROM snippet_package_version ".
				"WHERE snippet_package_id=$1", array($snippet_package_id));
			if (!$result || db_numrows($result) < 1) {
				//since no versions of this package remain,
				//delete the main package even if the user didn't create it
				$result=db_query_params("DELETE FROM snippet_package WHERE snippet_package_id=$1", array($snippet_package_id));
			}
			echo '<h1>Package Removed</h1>';
			snippet_footer(array());
			exit;
		}
	} else {
		exit_error('Error','Error - mangled URL?');
	}

} else {

	exit_not_logged_in();

}

?>