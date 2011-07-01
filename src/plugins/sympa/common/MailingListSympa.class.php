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
require_once 'Soapsympa.class.php';

class MailingListSympa extends Error {

	/**
	 * Associative array of data from db.
	 *
	 * @var	 array   $dataArray.
	 */
	var $dataArray;

	/**
	 * The Group object.
	 *
	 * @var	 object  $Group.
	 */
	var $Group;
	
	/**
	 * The mailing list id
	 *
	 * @var int $groupMailingListId
	 */
	var $groupMailingListId;

	/**
	 *  Constructor.
	 *
	 * @param	object	The Group object to which this mailing list is associated.
	 * @param	int		The group_list_id.
	 * @param	array		The associative array of data.
	 * @return	boolean	success.
	 */
	function MailingListSympa(&$Group, $groupListId = false, $dataArray = false) {
		$this->Error();
		if (!$Group || !is_object($Group)) {
			$this->setError(sprintf(_('%1$s:: No Valid Group Object'), 'MailingList'));
			return false;
		}
		if ($Group->isError()) {
			$this->setError('MailingList:: '.$Group->getErrorMessage());
			return false;
		}
		$this->Group =& $Group;

		if ($groupListId) {
			$this->groupMailingListId = $groupListId;
			if (!$dataArray || !is_array($dataArray)) {
				if (!$this->fetchData($groupListId)) {
					return false;
				}
			} else {
				$this->dataArray =& $dataArray;
				if ($this->dataArray['group_id'] != $this->Group->getID()) {
					$this->setError(_('Group_id in db result does not match Group Object'));
					$this->dataArray = null;
					return false;
				}
			}

		}
	
		return true;
	}

	/**
	 *	create - use this function to create a new entry in the database.
	 *
	 *	@param	string	The name of the mailing list
	 *	@param	string	The description of the mailing list
	 *	@param	int	Pass (1) if it should be public (0) for private.
	 *
	 *	@return	boolean	success.
	 */
	function create($listName, $description,$tabRoles,$creator_id=false) {
		//
		//	During the group creation, the current user_id will not match the admin's id
		//

		if (!$creator_id) {
			$creator_id=user_getid();
			if(!$this->userIsAdmin()) {
				$this->setPermissionDeniedError();
				return false;
			}
		}
	
 		/* Mis en commentaire le 05/11
		if(!$listName || strlen($listName) < MAIL__MAILING_LIST_NAME_MIN_LENGTH) {
			$this->setError(_('Must Provide List Name That Is 4 or More Characters Long'));
			return false;
		}
		*/
		

                if(!$listName || strlen($listName) == 0) {
                        $this->setError(_('Must Provide List Name'));
                        return false;
                }

                if(!$description || strlen($description) == 0) {
                        $this->setError(_('Must Provide Description'));
                        return false;
                }


                if(!$tabRoles || count($tabRoles) == 0) {
                        $this->setError(_('Must Provide Role'));
                        return false;
                }

		$realListName = strtolower($listName);		
		$listRoles = implode(',',$tabRoles);
		$listRoles = preg_replace("/[a-z]*/","",$listRoles); 
		if(!validate_email($realListName.'@'.$GLOBALS['sys_lists_host'])) {
			$this->setError(_('Invalid List Name') . ': ' .$realListName.'@'.$GLOBALS['sys_lists_host']);
			return false;
		}

		$result = db_query_params ('SELECT 1 FROM lists WHERE lower(list_name)=$1',
					   array ($realListName)) ;


		if (db_numrows($result) > 0) {
			$this->setError(_('List Already Exists'));
			return false;
		}
/*
		$result_forum_samename = db_query_params ('SELECT 1 FROM forum_group_list WHERE forum_name=$1 AND group_id=$2',
							  array ($listName,
								 $this->Group->getID())) ;

		if (db_numrows($result_forum_samename) > 0){
			$this->setError(_('Forum exists with the same name'));
			return false;
		}
*/
		$user = user_get_object($creator_id);
		$userEmail = $user->getEmail();

		$sympaSoap = new sympaSoap($userEmail);
		
		if ($sympaSoap->noexist($realListName)){
			$soapres = $sympaSoap->create($realListName,$description,$this->Group->getID(),$listRoles);
			if($soapres){
				db_begin();
				$result = db_query_params ('INSERT INTO lists (group_id,list_name,list_url,list_description) VALUES ($1,$2,$3,$4)',
							   array ($this->Group->getID(),
								  $realListName,
								  'https://'.$GLOBALS['sys_lists_host'].'/sympa/info/'.$realListName,
								  $description)) ;
				
				if (!$result) {
					db_rollback();
				$this->setError(sprintf(_('Error Creating %1$s'), _('Error Creating %1$s')).db_error());
				return false;
				}
				
				$this->groupMailingListId = db_insertid($result, 'lists', 'list_id');
	
				$this->fetchData($this->groupMailingListId);
				$user = user_get_object($creator_id);
				$userEmail = $user->getEmail();
				
				
				db_commit();
			}else{
				$this->setError(_('Error Error Creating'));	
				return false;
			}
			
		}else{
                      $this->setError(_('List Already Exists'));
	 	    return false;}
		
		return true;
		
	}

	/**
	 *  fetchData - re-fetch the data for this mailing list from the database.
	 *
	 *  @param  int	 The list_id.
	 *	@return	boolean	success.
	 */
	function fetchData($groupListId) {
		$res = db_query_params ('SELECT * FROM lists WHERE list_id=$1 AND group_id=$2',
					array ($groupListId,
					       $this->Group->getID())) ;
		if (!$res || db_numrows($res) < 1) {
			$this->setError(sprintf(_('Error Getting %1$s'), _('Error Getting %1$s')));
			return false;
		}
		$this->dataArray =& db_fetch_array($res);
		db_free_result($res);
		return true;
	}

	/**
	 *	update - use this function to update an entry in the database.
	 *
	 *	@param	string	The description of the mailing list
	 *	@param	int	Pass (1) if it should be public (0) for private
	 *	@return	boolean	success.
	 */
  /*
	function update($description) {
		if(! $this->userIsAdmin()) {
			$this->setPermissionDeniedError();
			return false;
		}
		
		$res = db_query_params ('UPDATE lists SET description=$1
			                 WHERE list_id=$2 AND group_id=$3',
					array ($description,
					       $this->groupMailingListId,
					       $this->Group->getID())) ;
		
		if (!$res || db_affected_rows($res) < 1) {
			$this->setError(_('Error On Update:').db_error());
			return false;
		}
		return true;
	}

*/
	/**
	 *	getGroup - get the Group object this mailing list is associated with.
	 *
	 *	@return	object	The Group object.
	 */
	function &getGroup() {
		return $this->Group;
	}

	/**
	 *	getID - The id of this mailing list
	 *
	 *	@return	int	The group_list_id #.
	 */
	function getID() {
		return $this->dataArray['list_id'];
	}


	/**
	 *	getName - get the name of this mailing list
	 *
	 *	@return string	The name of this mailing list
	 */
	function getName() {
		return $this->dataArray['list_name'];
	}


	/**
	 *	getDescription - get the description of this mailing list
	 *
	 *	@return string	The description.
	 */
	function getDescription() {
		return $this->dataArray['list_description'];
	}
	
	
	/**
	 * getListAdmin - get the user who is the admin of this mailing list
	 *
	 * @return User The admin user
	 */
	function getListAdmin() {
		return user_get_object($this->dataArray['list_admin']);
	}
	
	/**
	 * getStatus - get the status of this mailing list
	 *
	 * @return int The status
	 */
	function getStatus() {
		return $this->dataArray['status'];
	}
	
	/**
	 * getArchivesUrl - get the url to see the archives of the list
	 *
	 * @return string url of the archives
	 */
	function getArchivesUrl() {
			return 'http://'.$GLOBALS['sys_lists_host'].'/sympa/arc/'.$this->getName().'/';
	}
	
	/**
	 * getExternalInfoUrl - get the url to subscribe/unsubscribe
	 *
	 * @return string url of the info page
	 */
	function getExternalInfoUrl() {
		return 'http://'.$GLOBALS['sys_lists_host'].'/sympa/info/'.$this->getName();
	}
	
	/**
	 * getExternalAdminUrl - get the url to admin the list with the external tools used
	 *
	 * @return string url of the admin
	 */
	function getExternalAdminUrl() {
		return 'http://'.$GLOBALS['sys_lists_host'].'/sympa/admin/'.$this->getName();
	}

	/**
	 *	delete - permanently delete this mailing list
	 *
	 *	@param	boolean	listName.
	 *	@return	boolean success;
	 */

       function delete ($listName) {
          if (!$creator_id) {
                  $creator_id=user_getid();
                  if(!$this->userIsAdmin()) {
                           $this->setPermissionDeniedError();
                           return false;
                  }
           }

           if(!$listName || strlen($listName) == 0) {
                   $this->setError(_('Must Provide List Name'));
                   return false;
           }



            $user = user_get_object($creator_id);
            $userEmail = $user->getEmail();
            $sympaSoap = new sympaSoap($userEmail);

            $soapres = $sympaSoap->delete($listName);
            if($soapres){
                db_begin();

               $res = db_query_params ('INSERT INTO deleted_mailing_lists (mailing_list_name,delete_date,isdeleted) VALUES ($1,$2,$3)',
                                        array ($listName,
                                               time(),
                                               0)) ;
                if (!$res) {
                        $this->setError('Could Not Insert Row'.db_error());
                        return false;
                }

                $result = db_query_params ('DELETE FROM lists WHERE list_name=$1',
                                                           array($listName));

               if (!$result) {
                      db_rollback();
                      $this->setError(sprintf(_('Error Creating %1$s'), _('Error Creating %1$s')).db_error());
                      return false;
               }

               db_commit();
            }else{
                    $this->setError(_('Error Deleting'));
                     return false;
            }

            return true;

        }

	/**
	 * userIsAdmin - use this function to know if the user can administrate mailing lists
	 *
	 * This is a static method. Currently the user must be a project or a sitewide admin to administrate the mailing lists
	 *
	 * @return boolean true if the user can administrate mailing lists
	 */
	function userIsAdmin() {
		$perm = & $this->Group->getPermission(session_get_user());
		if (!$perm || !is_object($perm)) {
			return false;
		} elseif ($perm->isAdmin()) {
			return true;
		} else {
			return false;
		}
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
