#! /usr/bin/php
<?php
/**
 * GForge Group Docman updater
 *
 * Copyright 2004 GForge, LLC
 * http://fusionforge.org/
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  US
 */

require_once dirname(__FILE__).'/../www/env.inc.php';
require_once $gfcommon.'include/pre.php';

$res = db_query_params ('SELECT * FROM doc_data',
			array()) ;

if (!$res) {		// error
	echo db_error();
	exit(1);
} 

db_begin();
for ($i=0; $i < db_numrows($res); $i++) {
	$docid = db_result($res, $i, 'docid');
	$base64_data = db_result($res, $i, 'data');
	$data = base64_decode($base64_data);
	$size = strlen($data);
	
	$res2 = db_query_params ('UPDATE doc_data SET filesize=$1 WHERE docid=$2',
				 array ($size,
					$docid)) ;
	if (!$res2) {
		echo "Couldn't update document #".$docid.":".db_error()."\n";
		db_rollback();
		exit(1);
	}
}
echo "SUCCESS\n";
db_commit();

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
