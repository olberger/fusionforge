<?php
/*
 * key_id = 0 for all special sections (see writeRules).
 * key_id = 1 for SCM rules.
 * key_id = 2 for WebDAV rules.
 * 
 */
class Acl extends Error {
	var $group_id;
	var $key_id;
	var $section = array(1 => 'scm', 2 => 'dav');

	function Acl($group_id=0, $key_id=0) {
		$this->group_id=$group_id;
		$this->key_id=$key_id;
		return true;
	}

	function addRule($path, $role_id, $role_public, $role_anon) {
		$group_id = $this->group_id;
		$key_id   = $this->key_id;

		if (!$key_id) {
			$this->setError("INTERNAL ERROR: addRule() called with key_id=0 (all sections)");
			return false;
		}

		if ($path != trim($path)) {
			$this->setError(_('There are spaces around your path, this is not allowed.'));
			return false;
		}
		
		// Add an extra '/' at the beginning and eliminates duplicates // (if any).
		$path = preg_replace('/(\/+)/','/', '/'.$path);

		// Remove extra / at the end (if any).
		$path = preg_replace('/(.)\/$/', '\\1', $path);

		if ($path === '') {
			$this->setError(_("Field 'Path' is required."));
			return false;
		}
		if ($path === '/') {
			$this->setError(_("Can not create a rule for '/', use 'Edit Roles' instead."));
			return false;
		}
		
		db_begin();
		$sql = 'SELECT * FROM acl WHERE group_id=$1 AND key_id=$2 AND val=$3';
		$res = db_query_params($sql, array($group_id, $key_id, $path));
		if (db_numrows($res) > 0) {
			$this->setError(_('There is already a defined rule for this path, use the [Update] link instead.'));
			db_rollback();
			return false;
		}

		$sql = 'INSERT INTO acl (key_id, group_id, val, allow_public, allow_anon) VALUES ($1,$2,$3,$4,$5)';
		$res = db_query_params($sql, array($key_id, $group_id, $path, $role_public, $role_anon));
		if (!$res) {
			$this->setError('create::'.db_error());
			db_rollback();
			return false;
		}

		$acl_id=db_insertid($res,'acl','acl_id');
		if (!$acl_id) {
			$this->setError('create::db_insertid::'.db_error());
			db_rollback();
			return false;
		}

		$this->disableWriteRules();
		
		$group = group_get_object($group_id);
		foreach ($role_id as $k => $v) {
			if ($v != 100) {
				$role = new Role($group,$k);
				$role->setVal($this->section[$key_id], $acl_id, $v);
			}
		}
		db_commit();
		
		$this->enableWriteRules();

		$ret = $this->writeRules();
		if ($ret && $group->getIsExternal()) {
			$ret = $this->writeExternalRules();
		}
		return $ret;
	}

	function updateRule($acl_id, $role_id, $role_public, $role_anon) {
		$group_id = $this->group_id;
		$key_id   = $this->key_id;
		
		if (!$key_id) {
			$this->setError("INTERNAL ERROR: updateRule() called with key_id=0 (all sections)");
			return false;
		}

		db_begin();
		$sql = 'SELECT * FROM acl WHERE group_id=$1 AND key_id=$2 AND acl_id=$3';
		$res = db_query_params($sql, array($group_id, $key_id, $acl_id));
		if (db_numrows($res) != 1) {
			$this->setError(_('There is no already defined rule for this path.'));
			db_rollback();
			return false;
		}

		$sql = 'UPDATE acl SET allow_public=$1, allow_anon=$2 WHERE acl_id=$3';
		$res = db_query_params($sql, array($role_public, $role_anon, $acl_id));
		
		$this->disableWriteRules();
		
		$group = group_get_object($group_id);
		foreach ($role_id as $k => $v) {
			$role = new Role($group,$k);
			if ($v == 100) {
				$role->delVal($this->section[$key_id], $acl_id);
			} else {
				$role->setVal($this->section[$key_id], $acl_id, $v);
			}
		}
		db_commit();
		$this->enableWriteRules();

		$ret = $this->writeRules();
		if ($ret && $group->getIsExternal()) {
			$ret = $this->writeExternalRules();
		}
		return $ret;
	}

	function deleteRule($acl_id) {
		$group_id = $this->group_id;
		$key_id   = $this->key_id;
		
		if (!$key_id) {
			$this->setError("INTERNAL ERROR: deleteRule() called with key_id=0 (all sections)");
			return false;
		}

		$sql = 'SELECT * FROM acl WHERE group_id=$1 AND key_id=$2 AND acl_id=$3';
		$res = db_query_params($sql, array($group_id, $key_id, $acl_id));
		if (db_numrows($res) != 1) {
			$this->setError('Cannot delete rule, no such rule with id: '.$acl_id);
			return false;
		}
		$sql = 'DELETE FROM acl WHERE group_id=$1 AND key_id=$2 AND acl_id=$3';
		$res = db_query_params($sql, array($group_id, $key_id, $acl_id));
		
		$this->disableWriteRules();

		$group = group_get_object($group_id);
		$res=db_query_params('SELECT role_id FROM role WHERE group_id=$1', array($group_id));
		while($arr = db_fetch_array($res)) {
			$role = new Role($group, $arr['role_id']);
			$role->delVal($section, $acl_id);
		}
		$this->enableWriteRules();

		$ret = $this->writeRules();
		if ($ret && $group->getIsExternal()) {
			$ret = $this->writeExternalRules();
		}
		return $ret;
	}

	function disableWriteRules() {
		if (!isset($GLOBALS['acl_delayWriteRules'])) {
			$GLOBALS['acl_delayWriteRules'] = 0;
		}
		$GLOBALS['acl_delayWriteRules']++;
	}
	
	function enableWriteRules() {
		$GLOBALS['acl_delayWriteRules']--;
	}
	
	function writeAllRules() {
		$key = $this->key_id;
		
		// For the all sections, write rules for scm and dav.
		$this->key_id=1; 		// Write rules for scm.
		$this->writeRules();
		$this->writeExternalRules();

		$this->key_id=2;		// Write rules for dav.
		$this->writeRules();
		$this->writeExternalRules();
		
		$this->key_id=$key;		// Restore key_id.
		return true;
	}

	function writeRules() {
		global $gfconfig;
		if (isset($GLOBALS['acl_delayWriteRules']) && $GLOBALS['acl_delayWriteRules']) {
			return true;
		}

		if ($this->key_id == 1) {
			$file = $gfconfig.'/http/svnroot-access';
			$sql_groups = "SELECT is_public, enable_publicscm as public,enable_anonscm as anon,unix_group_name,groups.group_id 
				FROM groups, plugins, group_plugin 
				WHERE groups.status = 'A'
				AND groups.group_id=group_plugin.group_id
				AND group_plugin.plugin_id=plugins.plugin_id
				AND (plugins.plugin_name='scmsvn' OR plugins.plugin_name='websvn')";
			$filter = '';
		} elseif ($this->key_id == 2) {
			$file = $gfconfig.'/http/davroot-access';
			$sql_groups = "SELECT is_public, enable_publicdav as public,enable_anondav as anon,unix_group_name,group_id 
				FROM groups
				WHERE status = 'A'";
		} else {
			$this->setError("INTERNAL ERROR: Only key_id=1 or key_id=2 are supported right now.");
			return false;
		}

		if (is_file($file) && !is_writable($file)) {
			$this->setError("ERROR: File '$file' is not writable.");
			return false;
		}

		$tmpfile = tempnam( dirname($file), 'access');

		// Get the members of the roles.
		$sql = 'SELECT r.role_id, u.user_name
				FROM user_group ug, role r, users u, groups g
				WHERE ug.role_id=r.role_id
				AND ug.user_id=u.user_id
				AND ug.group_id=g.group_id
				AND g.status =$1
				AND r.is_external=$2
				ORDER BY role_id';
		$res = db_query_params($sql, array('A', 0));

		$content = "[groups]\n";
		$current = '';
		$groups = array();
		$users = array();
		while ( $row = db_fetch_array($res) ) {
			$grp = $row['role_id'];
			if ($grp != $current) {
				if ($current) 
					$content .= "\n";
				$content .= $row['role_id'].' = '.$row['user_name'];
				$groups[$grp] = 1;
				$current = $grp;
			} else {
				$content .= ', '.$row['user_name'];
			}
			$users[ $row['user_name'] ] = 1;
		}
		
		// Add all other users in a specific group 'others' (for rule everyone).
		$sql = 'SELECT user_name FROM users WHERE status=$1 AND is_external=$2';
		$res = db_query_params($sql, array('A', 0));
		$others = array();
		while ( $row = db_fetch_array($res) ) {
			if (!isset($users[ $row['user_name'] ])) {
				$others[] = $row['user_name'];
			}
		}
		unset($users);
		$groups['others'] = 1;
		
		$content .= "\n".'others = '.join(', ', $others);
		$content .= "\n".'everyone = @'.join(', @', array_keys($groups));
		
		$content .= "\n\n[/]\n";
		// Give rw to global admins (admins of the group_id=1)
		$sql = 'SELECT r.role_id
				FROM groups g, role r, role_setting rs
				WHERE g.group_id=$1
				AND g.group_id=r.group_id
				AND r.role_id=rs.role_id
				AND section_name=$2
				AND value=$3';
		$res = db_query_params($sql, array(1, 'projectadmin', 'A'));
		while ( $r = db_fetch_array($res) ) {
			$content .= '@'.$r['role_id'].' = rw'."\n";
		}
		$content .= "* = \n";
		
		// Get all the ACL rules for all project and write all the rules that
		// don't have any roles inside (for the anon and public clauses).
		$sql = 'SELECT * FROM acl WHERE key_id=$1';
		$res = db_query($sql, array($this->key_id));
		$acl = array();
		while ( $r = db_fetch_array($res) ) {
			$acl[ $r['group_id'] ][] = $r;
		}
		
		// Preload roles for all groups to reduce SQL queries.
		$sql = 'SELECT r.role_id, r.group_id, rs.value
				FROM role r, role_setting rs
				WHERE r.role_id=rs.role_id
				AND ref_id=$1
				AND section_name=$2';
		$role_rows_root = $this->sqlToArrayByGroupId($sql, array(0, $this->section[$this->key_id]));

		$sql = 'SELECT r.role_id, r.group_id, a.acl_id, a.val, a.allow_public, a.allow_anon, rs.value
				FROM acl a, role r, role_setting rs
				WHERE r.role_id=rs.role_id
				AND a.acl_id=rs.ref_id
				AND section_name=$1
				ORDER BY val';
		$role_rows_acl = $this->sqlToArrayByGroupId($sql, array($this->section[$this->key_id]));

		$res = db_query($sql_groups);
		while ( $row = db_fetch_array($res) ) {
			$prj = $row['unix_group_name'];

			$head = "\n".'['.$prj.':/]'."\n";
			$text = '';

			// Write the default rule (for /) for the project.
			if (isset($role_rows_root[ $row['group_id'] ])) {
				foreach($role_rows_root[ $row['group_id'] ] as $r) {
					if (!isset($groups[$r['role_id']]))
						continue;
					if ($r['value'] == 0)
						$rights = 'r';
					elseif ($r['value'] == 1) 
						$rights = 'rw';
					elseif ($r['value'] == -1)
						$rights = '';
					else 
						echo "Illegal value found: ".$r['value']; 
					$text .= '@'.$r['role_id'].' = '.$rights."\n";
				}
				if ($row['is_public'] == 1) {
					$text .= $this->_getObserversRules($row['public'],$row['anon']);
				}
				if ($text) 
					$content .= $head.$text;
			}

			// Write defined ACL rules for the project with roles.
			$done = array();
			if (isset($role_rows_acl[ $row['group_id'] ])) {
				$current = '';
				$observers = '';
				foreach($role_rows_acl[ $row['group_id'] ] as $r) {
					$grp = '['.$prj.':'.$r['val'].']';
					if (!isset($groups[$r['role_id']]))
						continue;
					if ($grp != $current) {
						$content .= "$observers\n$grp\n";
						$current = $grp;
						$observers = $this->_getObserversRules($r['allow_public'],$r['allow_anon']);
					}
					if ($r['value'] == 0)
						$rights = 'r';
					elseif ($r['value'] == 1) 
						$rights = 'rw';
					elseif ($r['value'] == -1)
						$rights = '';
					else 
						$this->setError("INTERNAL ERROR: Illegal value found: ".$r['value']);
					$content .= '@'.$r['role_id'].' = '.$rights."\n";
					
					$done[ $r['acl_id']] = 1;
				}
				if ($observers) {
					$content .= $observers;
				}
			}

			// Get all the ACL rules for the project and write all the rules that
			// don't have any roles inside (for the anon and public clauses).
			if (isset($acl[ $row['group_id'] ])) {
				foreach($acl[ $row['group_id'] ] as $r) {
					if (! isset($done[ $r['acl_id']])) {
						$grp = '['.$prj.':'.$r['val'].']';
						$content .= "\n$grp\n";
						$content .= $this->_getObserversRules($r['allow_public'],$r['allow_anon']);
					}
				}
			}
		}

		$ret = file_put_contents($tmpfile, $content);
		if (!$ret) {
			unlink ($tmpfile);
			$this->setError("ERROR: fclose error, access file not saved.");
			return false;
		}
		return rename($tmpfile, $file);
	}

	function writeExternalRules() {
		global $gfconfig;
		if (isset($GLOBALS['acl_delayWriteRules']) && $GLOBALS['acl_delayWriteRules']) {
			return true;
		}
		if ($this->key_id == 1) {
			$file = $gfconfig.'/http/svnroot-access-external';
			$sql_groups = "SELECT is_public, enable_publicscm as public,enable_anonscm as anon,unix_group_name,groups.group_id 
				FROM groups, plugins, group_plugin 
				WHERE groups.status = 'A'
				AND groups.group_id=group_plugin.group_id
				AND group_plugin.plugin_id=plugins.plugin_id
				AND (plugins.plugin_name='scmsvn' OR plugins.plugin_name='websvn')";
			$filter = '';
		} elseif ($this->key_id == 2) {
			$file = $gfconfig.'/http/davroot-access-external';
			$sql_groups = "SELECT is_public, enable_publicdav as public,enable_anondav as anon,unix_group_name,group_id 
				FROM groups
				WHERE status = 'A'";
		} else {
			$this->setError("INTERNAL ERROR: Only key_id=1 or key_id=2 are supported right now.");
			return false;
		}

		if (is_file($file) && !is_writable($file)) {
			$this->setError("ERROR: File '$file' is not writable.");
			return false;
		}
		$tmpfile = tempnam( dirname($file), 'access');
		
		$fd = fopen($tmpfile, 'w');
				
		// Get the members of the roles.
		$sql = "SELECT r.role_id, u.user_name, r.is_external
				FROM user_group ug, role r, users u, groups g
				WHERE ug.role_id=r.role_id
				AND ug.user_id=u.user_id
				AND ug.group_id=g.group_id
				AND g.status = 'A'
				AND r.is_external = 1
				ORDER BY role_id";
		$res = db_query($sql);

		$content = "[groups]\n";
		$current = '';
		$groups = array();
		while ( $row = db_fetch_array($res) ) {
			$grp = $row['role_id'];
			if ($grp != $current) {
				if ($current) 
					$content .= "\n";
				$content .= $row['role_id'].' = '.$row['user_name'];
				$groups[] = $grp;
				$current = $grp;
			} else {
				$content .= ', '.$row['user_name'];
			}
		}
		
		$content .= "\n\n[/]\n";
		$content .= "* = \n";
		
		$sql = "SELECT r.role_id, r.group_id, a.acl_id, a.val, rs.value
				FROM acl a, role r, role_setting rs
				WHERE r.role_id=rs.role_id
				AND r.is_external=1
				AND a.acl_id=rs.ref_id
				AND section_name = '".$this->section[$this->key_id]."'
				ORDER BY val";
		$role_rows_root = $this->sqlToArrayByGroupId($sql);

		$res = db_query($sql_groups);
		while ( $row = db_fetch_array($res) ) {
			$prj = $row['unix_group_name'];
				
			// Write defined ACL rules for the project with roles.
			$current = '';
			$done = array();
			if (isset($role_rows_root[ $row['group_id'] ])) {
				foreach($role_rows_root[ $row['group_id'] ] as $r) {
					$grp = '['.$prj.':'.$r['val'].']';
					if (!in_array($r['role_id'], $groups))
						continue;
					if (($r['val'] !== '/third-parties') &&
						(strpos($r['val'], '/third-parties/') !== 0))
						continue;
					if ($grp != $current) {
						$content .= "\n$grp\n";
						$current = $grp;
					}
					if ($r['value'] == 0)
						$rights = 'r';
					elseif ($r['value'] == 1)
						$rights = 'rw';
					elseif ($r['value'] == -1)
						$rights = '';
					else
						$this->setError("INTERNAL ERROR: Illegal value found: ".$r['value']);
					$content .='@'.$r['role_id'].' = '.$rights."\n";

					$done[ $r['acl_id']] = 1;
				}
			}
		}

		$ret = file_put_contents($tmpfile, $content);
		if (!$ret) {
			unlink ($tmpfile);
			$this->setError("ERROR: fclose error, access file not saved.");
			return false;
		}
		return rename($tmpfile, $file);
	}

	function _getObserversRules($public, $anon) {
		if ($public==1 && $anon==1) 
			return "* = r\n";
		if ($public==1 && $anon==0) 
			return "@everyone = r\n* =\n";
		if ($public==0)
			return "* =\n";
	}

	private function sqlToArrayByGroupId($sql, $params) {
		$array = array();
		$res = db_query_params($sql, $params);
		while ( $r = db_fetch_array($res) ) {
			$array[ $r['group_id'] ][] = $r;
		}
		return $array;
	}
}
