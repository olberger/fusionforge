<?php

/**
 * ContribTrackerPlugin Class
 *
 * Copyright 2009, Roland Mas
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

class ContribTrackerPlugin extends Plugin {
	function ContribTrackerPlugin () {
		$this->Plugin() ;
		$this->name = "contribtracker" ;
		$this->text = "Contribution Tracker" ; // To show in the tabs, use...
		$this->hooks[] = "groupmenu" ;	// To put into the project tabs
		$this->hooks[] = "groupisactivecheckbox" ; // The "use ..." checkbox in editgroupinfo
		$this->hooks[] = "groupisactivecheckboxpost" ; //
		$this->hooks[] = "project_admin_plugins"; // to show up in the admin page for group
	}

	function CallHook ($hookname, $params) {
		if ($hookname == "groupmenu") {
			$group_id=$params['group'];
			$project = &group_get_object($group_id);
			if (!$project || !is_object($project)) {
				return;
			}
			if ($project->isError()) {
				return;
			}
			if (!$project->isProject()) {
				return;
			}
			if ( $project->usesPlugin ( $this->name ) ) {
				$params['TITLES'][] = _('Contribution tracker') ;
				$params['DIRS'][]='/plugins/contribtracker/index.php?type=group&id=' . $group_id . "&pluginname=" . $this->name; // we indicate the part we're calling is the project one
			}	
			(($params['toptab'] == $this->name) ? $params['selected']=(count($params['TITLES'])-1) : '' );
		} elseif ($hookname == "groupisactivecheckbox") {
			//Check if the group is active
			// this code creates the checkbox in the project edit public info page to activate/deactivate the plugin
			$group_id=$params['group'];
			$group = &group_get_object($group_id);
			echo "<tr>";
			echo "<td>";
			echo ' <input type="CHECKBOX" name="use_contribtrackerplugin" value="1" ';
			// CHECKED OR UNCHECKED?
			if ( $group->usesPlugin ( $this->name ) ) {
				echo "CHECKED";
			}
			echo "><br/>";
			echo "</td>";
			echo "<td>";
			echo "<strong>Use ".$this->text." Plugin</strong>";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "groupisactivecheckboxpost") {
			// this code actually activates/deactivates the plugin after the form was submitted in the project edit public info page
			$group_id=$params['group'];
			$group = &group_get_object($group_id);
			$use_contribtrackerplugin = getStringFromRequest('use_contribtrackerplugin');
			if ( $use_contribtrackerplugin == 1 ) {
				$group->setPluginUse ( $this->name );
			} else {
				$group->setPluginUse ( $this->name, false );
			}
		} elseif ($hookname == "project_admin_plugins") {
			// this displays the link in the project admin options page to its ContribTracker administration
			$group_id = $params['group_id'];
			$group = &group_get_object($group_id);
			if ( $group->usesPlugin ( $this->name ) ) {
				echo util_make_link ("/plugins/contribtracker/index.php?id=".$group->getID().'&type=admin&pluginname='.$this->name,
						     _('View the ContribTracker Administration')) ;
				echo '</p>';
			}
		}												    
		elseif ($hookname == "blahblahblah") {
			// ...
		} 
	}
}

class ContribTrackerRole extends Error {
	var $data_array ;

	function ContribTrackerRole ($id=false) {
		$this->Error () ;
		if (!$id) {
			return true ;
		}
		return $this->fetchData ($id) ;
	}

	function fetchData ($id) {
		$res = db_query_params ('SELECT * FROM plugin_contribtracker_role WHERE role_id=$1',
					array ($id)) ;
		if (!$res || db_numrows($res) < 1) {
			$this->setError(sprintf('ContribTrackerRole(): %s',db_error()));
			return false;
		}
		
		$this->data_array = db_fetch_array ($res) ;
		return true ;
	}

	function create ($name, $description) {
		if ($this->getId () != 0) {
			$this->setError(_('Object already exists')) ;
			return false ;
		}

		db_begin () ;
		$res = db_query_params ('INSERT INTO plugin_contribtracker_role (name, description) VALUES ($1,$2)',
					array ($name,
					       $description)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}

		$id = db_insertid ($res, 'plugin_contribtracker_role', 'role_id') ;
		if (!$id) {
			$this->setError (sprintf(_('Could not get ID from object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function update ($name, $description) {
		if (! $this->getId ()) {
			$this->setError(_('Object does not exist')) ;
			return false ;
		}

		$id = $this->getId () ;

		db_begin () ;
		$res = db_query_params ('UPDATE plugin_contribtracker_role SET (name, description) = ($1,$2) WHERE role_id = $3',
					array ($name,
					       $description,
					       $id)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not update object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function delete () {
		$id = $this->getId () ;
		if (!$id) {
			$this->setError (_('Cannot delete a non-existing object')) ;
			return false ;
		}

		$res = db_query_params ('DELETE FROM plugin_contribtracker_role WHERE role_id = $1',
					array ($id)) ;
		if (!$res) {
			$this->setError (sprintf(_('Could not delete object in database: %s'),
						 db_error ())) ;
			return false ;
		}

		$this->data_array = array () ;
		
		return true ;
	}

	function getId () {
		if (isset ($this->data_array['role_id'])) {
			return $this->data_array['role_id'] ;
		} else {
			return false ;
		}
	}
	function getName () { return $this->data_array['name'] ; }
	function getDescription () { return $this->data_array['description'] ; }
	
}

class ContribTrackerLegalStructure extends Error {
	var $data_array ;

	function ContribTrackerLegalStructure ($id=false) {
		$this->Error () ;
		if (!$id) {
			return true ;
		}
		return $this->fetchData ($id) ;
	}

	function fetchData ($id) {
		$res = db_query_params ('SELECT * FROM plugin_contribtracker_legal_structure WHERE struct_id=$1',
					array ($id)) ;
		if (!$res || db_numrows($res) < 1) {
			$this->setError(sprintf('ContribTrackerLegalStructure(): %s',db_error()));
			return false;
		}
		
		$this->data_array = db_fetch_array ($res) ;
		return true ;
	}

	function create ($name) {
		if ($this->getId () != 0) {
			$this->setError(_('Object already exists')) ;
			return false ;
		}

		db_begin () ;
		$res = db_query_params ('INSERT INTO plugin_contribtracker_legal_structure (name) VALUES ($1)',
					array ($name)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}

		$id = db_insertid ($res, 'plugin_contribtracker_legal_structure', 'struct_id') ;
		if (!$id) {
			$this->setError (sprintf(_('Could not get ID from object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
		
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function update ($name) {
		if (! $this->getId ()) {
			$this->setError(_('Object does not exist')) ;
			return false ;
		}

		$id = $this->getId () ;

		db_begin () ;
		$res = db_query_params ('UPDATE plugin_contribtracker_legal_structure SET (name) = ($1) WHERE struct_id = $2',
					array ($name,
					       $id)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not update object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
		
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function delete () {
		$id = $this->getId () ;
		if (!$id) {
			$this->setError (_('Cannot delete a non-existing object')) ;
			return false ;
		}

		$res = db_query_params ('DELETE FROM plugin_contribtracker_legal_structure WHERE struct_id = $1',
					array ($id)) ;
		if (!$res) {
			$this->setError (sprintf(_('Could not delete object in database: %s'),
						 db_error ())) ;
			return false ;
		}

		$this->data_array = array () ;
		
		return true ;
	}

	function getId () {
		if (isset ($this->data_array['struct_id'])) {
			return $this->data_array['struct_id'] ;
		} else {
			return false ;

	}
	function getName () { return $this->data_array['name'] ; }
}

class ContribTrackerActor extends Error {
	var $data_array ;

	function ContribTrackerActor ($id=false) {
		$this->Error () ;
		if (!$id) {
			return true ;
		}
		return $this->fetchData ($id) ;
	}

	function fetchData ($id) {
		$res = db_query_params ('SELECT * FROM plugin_contribtracker_actor WHERE actor_id=$1',
					array ($id)) ;
		if (!$res || db_numrows($res) < 1) {
			$this->setError(sprintf('ContribTrackerActor(): %s',db_error()));
			return false;
		}
		
		$this->data_array = db_fetch_array ($res) ;
		return true ;
	}

	function create ($name, $address, $email, $description, $structure) {
		if ($this->getId () != 0) {
			$this->setError(_('Object already exists')) ;
			return false ;
		}

		db_begin () ;
		$res = db_query_params ('INSERT INTO plugin_contribtracker_actor (name,address,email,description,struct_id) VALUES ($1,$2,$3,$4,$5)',
					array ($name,
					       $address,
					       $email,
					       $description,
					       $structure->getID())) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}

		$id = db_insertid ($res, 'plugin_contribtracker_actor', 'actor_id') ;
		if (!$id) {
			$this->setError (sprintf(_('Could not get ID from object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function update ($name, $address, $email, $description, $structure) {
		if (! $this->getId ()) {
			$this->setError(_('Object does not exist')) ;
			return false ;
		}

		$id = $this->getId () ;

		db_begin () ;
		$res = db_query_params ('UPDATE plugin_contribtracker_actor SET (name,address,email,description,struct_id) = ($1,$2,$3,$4,$5) WHERE actor_id = $6',
					array ($name,
					       $address,
					       $email,
					       $description,
					       $structure->getID(),
					       $id)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object in update: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function delete () {
		$id = $this->getId () ;
		if (!$id) {
			$this->setError (_('Cannot delete a non-existing object')) ;
			return false ;
		}

		$res = db_query_params ('DELETE FROM plugin_contribtracker_actor WHERE actor_id = $1',
					array ($id)) ;
		if (!$res) {
			$this->setError (sprintf(_('Could not delete object in database: %s'),
						 db_error ())) ;
			return false ;
		}

		$this->data_array = array () ;
		
		return true ;
	}

	function getId () {
		if (isset ($this->data_array['actor_id'])) {
			return $this->data_array['actor_id'] ;
		} else {
			return false ;

	}
	function getName () { return $this->data_array['name'] ; }
	function getAddress () { return $this->data_array['address'] ; }
	function getEmail () { return $this->data_array['email'] ; }
	function getDescription () { return $this->data_array['description'] ; }
	function getLegalStructure () {
		return new ContribTrackerLegalStructure ($this->data_array['struct_id']) ;
	}
}

class ContribTrackerContribution extends Error {
	var $data_array ;

	function ContribTrackerContribution ($id=false) {
		$this->Error () ;
		if (!$id) {
			return true ;
		}
		return $this->fetchData ($id) ;
	}

	function fetchData ($id) {
		$res = db_query_params ('SELECT * FROM plugin_contribtracker_contribution WHERE contrib_id=$1',
					array ($id)) ;
		if (!$res || db_numrows($res) < 1) {
			$this->setError(sprintf('ContribTrackerContribution(): %s',db_error()));
			return false;
		}
		
		$this->data_array = db_fetch_array ($res) ;
		return true ;
	}

	function create ($name, $date, $description, $group) {
		if ($this->getId () != 0) {
			$this->setError(_('Object already exists')) ;
			return false ;
		}

		db_begin () ;
		$res = db_query_params ('INSERT INTO plugin_contribtracker_contribution (name,date,description,group_id) VALUES ($1,$2,$3,$4)',
					array ($name,
					       $date,
					       $description,
					       $group->getID())) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}

		$id = db_insertid ($res, 'plugin_contribtracker_contribution', 'contrib_id') ;
		if (!$id) {
			$this->setError (sprintf(_('Could not get ID from object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function update ($name, $date, $description, $group) {
		if (! $this->getId ()) {
			$this->setError(_('Object does not exist')) ;
			return false ;
		}

		$id = $this->getId () ;

		db_begin () ;
		$res = db_query_params ('UPDATE plugin_contribtracker_contribution SET (name,date,description,group_id) = ($1,$2,$3,$4) WHERE contrib_id = $5',
					array ($name,
					       $date,
					       $description,
					       $group->getID(),
					       $id)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object update database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function delete () {
		$id = $this->getId () ;
		if (!$id) {
			$this->setError (_('Cannot delete a non-existing object')) ;
			return false ;
		}

		$res = db_query_params ('DELETE FROM plugin_contribtracker_contribution WHERE contrib_id = $1',
					array ($id)) ;
		if (!$res) {
			$this->setError (sprintf(_('Could not delete object in database: %s'),
						 db_error ())) ;
			return false ;
		}

		$this->data_array = array () ;
		
		return true ;
	}

	function getId () {
		if (isset ($this->data_array['contrib_id'])) {
			return $this->data_array['contrib_id'] ;
		} else {
			return false ;

	}
	function getName () { return $this->data_array['name'] ; }
	function getDate () { return $this->data_array['date'] ; }
	function getDescription () { return $this->data_array['description'] ; }
	function getGroup () {
		return group_get_object ($this->data_array['group_id']) ;
	}
}

class ContribTrackerParticipation extends Error {
	var $data_array ;

	function ContribTrackerParticipation ($id=false) {
		$this->Error () ;
		if (!$id) {
			return true ;
		}
		return $this->fetchData ($id) ;
	}

	function fetchData ($id) {
		$res = db_query_params ('SELECT * FROM plugin_contribtracker_participation WHERE participation_id=$1',
					array ($id)) ;
		if (!$res || db_numrows($res) < 1) {
			$this->setError(sprintf('ContribTrackerParticipation(): %s',db_error()));
			return false;
		}
		
		$this->data_array = db_fetch_array ($res) ;
		return true ;
	}

	function create ($contrib,$actor,$role) {
		if ($this->getId () != 0) {
			$this->setError(_('Object already exists')) ;
			return false ;
		}

		db_begin () ;
		$res = db_query_params ('INSERT INTO plugin_contribtracker_participation (contrib_id,actor_id,role_id) VALUES ($1,$2,$3)',
					array ($contrib->getID(),
					       $actor->getID(),
					       $role->getID())) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}

		$id = db_insertid ($res, 'plugin_contribtracker_participation', 'participation_id') ;
		if (!$id) {
			$this->setError (sprintf(_('Could not get ID from object in database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function update ($contrib, $actor, $role) {
		if (! $this->getId ()) {
			$this->setError(_('Object does not exist')) ;
			return false ;
		}

		$id = $this->getId () ;

		db_begin () ;
		$res = db_query_params ('UPDATE plugin_contribtracker_participation SET (contrib_id,actor_id,role_id) = ($1,$2,$3) WHERE participation_id = $4',
					array ($contrib->getID(),
					       $actor->getID(),
					       $role->getID(),
					       $id)) ;
		if (!$res || db_affected_rows ($res) < 1) {
			$this->setError (sprintf(_('Could not create object update database: %s'),
						 db_error ()));
			db_rollback () ;
			return false ;
		}
			
		db_commit () ;
		return $this->fetchData ($id) ;
	}

	function delete () {
		$id = $this->getId () ;
		if (!$id) {
			$this->setError (_('Cannot delete a non-existing object')) ;
			return false ;
		}

		$res = db_query_params ('DELETE FROM plugin_contribtracker_participation WHERE participation_id = $1',
					array ($id)) ;
		if (!$res) {
			$this->setError (sprintf(_('Could not delete object in database: %s'),
						 db_error ())) ;
			return false ;
		}

		$this->data_array = array () ;
		
		return true ;
	}

	function getId () {
		if (isset ($this->data_array['participation_id'])) {
			return $this->data_array['participation_id'] ;
		} else {
			return false ;
		}
	}
	function getActor () {
		return new ContribTrackerActor ($this->data_array['actor_id']) ;
	}
	function getRole () {
		return new ContribTrackerRole ($this->data_array['role_id']) ;
	}
	function getContribution () {
		return new ContribTrackerContribution ($this->data_array['contrib_id']) ;
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
