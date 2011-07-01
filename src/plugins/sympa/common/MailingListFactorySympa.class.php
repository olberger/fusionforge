<?php
/**
 * FusionForge mailing lists
 *
 * Copyright 2002, Tim Perdue/GForge, LLC
 * Copyright 2003, Guillaume Smet
 * Copyright 2009, Roland Mas
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 * 
 * FusionForge is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FusionForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */

require_once $gfcommon.'include/Error.class.php';
require_once 'MailingListSympa.class.php';

class MailingListFactorySympa extends Error {

	/**
	 * The Group object.
	 *
	 * @var	 object  $Group.
	 */
	var $Group;

	/**
	 * The mailing lists array.
	 *
	 * @var	 array	$mailingLists.
	 */
	var $mailingLists;


	/**
	 *  Constructor.
	 *
	 *	@param	object	The Group object to which these mailing lists are associated.
	 */
	function MailingListFactorySympa(& $Group) {
		$this->Error();
		
		if (!$Group || !is_object($Group)) {
			$this->setError(sprintf(_('%1$s:: No Valid Group Object'), 'MailingListFactorySympa'));
			return false;
		}
		if ($Group->isError()) {
			$this->setError('MailingListFactorySympa:: '.$Group->getErrorMessage());
			return false;
		}
		$this->Group =& $Group;

		return true;
	}

	/**
	 *	getGroup - get the Group object this MailingListFactory is associated with.
	 *
	 *	@return object	The Group object.
	 */
	function &getGroup() {
		return $this->Group;
	}

	/**
	 *	getMailingLists - get an array of MailingList objects for this Group.
	 *
	 * @param boolean $admin if we are in admin mode (we want to see deleted lists)
	 *	@return	array	The array of MailingList objects.
	 */
	function &getMailingListsSympa() {
		if (isset($this->mailingListsSympa) && is_array($this->mailingListsSympa)) {
			return $this->mailingListsSympa;
		}
		

		$perm = & $this->Group->getPermission(session_get_user());

		$result = db_query_params ('SELECT * FROM lists WHERE group_id=$1 ORDER BY list_name',
					   array ($this->Group->getID())) ;

		if (!$result) {
			$this->setError(sprintf(_('Error Getting %1$s'), _('Error Getting %1$s')).db_error());
			return false;
		} else {
			$this->mailingLists = array();
			while ($arr = db_fetch_array($result)) {
				$this->mailingLists[] = new MailingListSympa($this->Group, $arr['list_id'], $arr);
			}
		}
		return $this->mailingLists;
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
