<?php
/** External authentication via LDAP for FusionForge
 * Copyright 2003, Roland Mas <lolando@debian.org>
 * Copyright 2004, Roland Mas <roland@gnurandal.com> 
 *                 The Gforge Group, LLC <http://gforgegroup.com/>
 * Copyright 2004, Christian Bayle <bayle@debian.org>
 * Copyright 2009-2010, Alain Peyrat, Alcatel-Lucent
 * Copyright 2009, Chris Dalzell, OpenGameForge.org
 * Copyright 2011, Roland Mas
 *
 * This file is part of FusionForge
 *
 * This plugin, like FusionForge, is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
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

require_once $GLOBALS['gfcommon'].'include/User.class.php';

class LdapextauthPlugin extends ForgeAuthPlugin {
	protected $saved_login;
	protected $saved_password;
	protected $saved_user;
	protected $saved_data;

	function LdapextauthPlugin () {
		global $gfconfig;
		$this->ForgeAuthPlugin() ;
		$this->name = "ldapextauth";
		$this->text = "LDAP authentication";

		$this->_addHook('display_auth_form');
		$this->_addHook("check_auth_session");
		$this->_addHook("fetch_authenticated_user");
		$this->_addHook("sync_account_info");
		$this->_addHook("close_auth_session");

		$this->cookie_name = 'forge_session_ldapextauth';

		$this->ldap_conn = false ;
		$this->saved_login = '';
		$this->saved_password = '';
		$this->saved_user = NULL;
		$this->saved_data = array();

		$this->declareConfigVars();
	}
	
	function syncAccountInfo($params) {
		if (!$this->syncDataOn($params['event'])) {
			return true;
		}
		$u = $params['user'];
		$data = $this->saved_data;

		if (!$data) {
			$data = $this->fetchDataForUser($u->getUnixName());
		}

		if (!$data) {
			error_log("No data to sync from LDAP for user ".$u->getUnixName());
			return true;
		}

		if ($u->getStatus() == 'D') {
			$u->setStatus('A');
		}
		if (!session_check_credentials_in_database($this->saved_login, $this->saved_password, false)) {
			$u->setPasswd($this->saved_password);
		}
		
		$mapped_data = array(
			'username' => $u->getUnixName(),
			'firstname' => $u->getFirstName(),
			'lastname' => $u->getLastName(),
			'email' => $u->getEmail(),
			'phone' => $u->getPhone()
			);
			
		foreach (explode(',', forge_get_config('mapping', $this->name))
			 as $map_entry) {
			list ($fffield, $ldapfield) = explode('=',$map_entry);
			$mapped_data[$fffield] = $this->saved_data[$ldapfield][0];
		}
		
		$u->update($mapped_data['firstname'],
			   $mapped_data['lastname'],
			   $u->getLanguage(),
			   $u->getTimeZone(),
			   $u->getMailingsPrefs('site'),
			   $u->getMailingsPrefs('va'),
			   $u->usesRatings(),
			   $u->getJabberAddress(),
			   $u->getJabberOnly(),
			   $u->getThemeID(),
			   $u->getAddress(),
			   $u->getAddress2(),
			   $mapped_data['phone'],
			   $u->getFax(),
			   $u->getTitle(),
			   $u->getCountryCode(),
			   $mapped_data['email']);
	}

	function displayAuthForm($params) {
		if (!$this->isRequired() && !$this->isSufficient()) {
			return true;
		}
		$return_to = $params['return_to'];
		$loginname = '';

		echo '<h2>'._('LDAP authentication').'</h2>';
		echo '<p>';
		echo _('Cookies must be enabled past this point.');
		echo '</p>';

		echo '<form action="' . util_make_url('/plugins/ldapextauth/post-login.php') . '" method="post">
<input type="hidden" name="form_key" value="' . form_generate_key() . '"/>
<input type="hidden" name="return_to" value="' . htmlspecialchars(stripslashes($return_to)) . '" />
<p>';
		echo _('LDAP Login name:');
		echo '<br /><input type="text" name="form_loginname" value="' . htmlspecialchars(stripslashes($loginname)) . '" /></p><p>' . _('Password:') . '<br /><input type="password" name="form_pw" /></p><p><input type="submit" name="login" value="' . _('Login') . '" />
</p>
</form>' ;
	}

	protected function declareConfigVars() {
		parent::declareConfigVars();

		forge_define_config_item ('start_tls', $this->name, 'no');
		forge_set_config_item_bool ('start_tls', $this->name) ;

		forge_define_config_item ('ldap_server', $this->name, 'ldap.example.com');
		forge_define_config_item ('ldap_port', $this->name, 389);
		forge_define_config_item ('base_dn', $this->name, 'ou=users,dc=example,dc=com');
		forge_define_config_item ('skipped_users', $this->name, '');
		forge_define_config_item ('manager_dn', $this->name, '');
		forge_define_config_item ('manager_password', $this->name, '');
	}

	/// HELPERS

	function fetchDataForUser($loginname) {
		if (!$this->ConnectLdap()) {
			return false;
		}

		if (forge_get_config('manager_dn', $this->name)) {
			ldap_bind($this->ldap_conn, 
				   forge_get_config('manager_dn', $this->name),
				   forge_get_config('manager_password', $this->name));
		} else {
			ldap_bind($this->ldap_conn);
		}

		$fieldname = 'uid';
		foreach (explode(',', forge_get_config('mapping', $this->name))
			 as $map_entry) {
			list ($fffield, $ldapfield) = explode('=',$map_entry);
			if ($fffield = 'username') {
				$fieldname = $ldapfield;
			}
		}

		$res = ldap_search($this->ldap_conn, forge_get_config('base_dn', $this->name), "($ldapfield=$loginname)") ;
		if (!$res || ldap_count_entries($this->ldap_conn, $res) == 0) {
			// No user by that name in LDAP directory
			return false;
		}
		$info = ldap_get_entries ($this->ldap_conn,$res);
		$data = $info[0];
		return $data;
	}

	function checkLDAPCredentials($loginname, $passwd) {
		if (!$this->ConnectLdap()) {
			// No connection to LDAP directory
			if ($this->isRequired()) {
				return FORGE_AUTH_AUTHORITATIVE_REJECT;
			} else {
				return FORGE_AUTH_NOT_AUTHORITATIVE;
			}
		}

		$data = fetchDataForUser($loginname) ;
		if (!$data) {
			return FORGE_AUTH_AUTHORITATIVE_REJECT;
		}

		if (@ldap_bind($this->ldap_conn, $data['dn'], $passwd)) {
			// OK
			$this->saved_data = $data;
			$this->saved_password = $passwd;
			return FORGE_AUTH_AUTHORITATIVE_ACCEPT;
		} else {
			// Probably invalid password
			return FORGE_AUTH_AUTHORITATIVE_REJECT;
		}
	}

	function ConnectLDAP() {
		if ($this->ldap_conn) {
			return true;
		}

		$server = forge_get_config('ldap_server', $this->name);
		$port = forge_get_config('ldap_port', $this->name);
		if ($port) {
			$conn = ldap_connect ($server, $port);
		} else {
			$conn = ldap_connect ($server);
		}

		if (forge_get_config('ldap_version')) {
			if (!ldap_set_option ($conn, LDAP_OPT_PROTOCOL_VERSION, forge_get_config('ldap_version'))) {
				return false;
			}
		}

		if (forge_get_config('start_tls', $this->name)) {
			if (!ldap_start_tls($conn)) {
				return false;
			}
		}

		// If the ldap server does not allow anonymous bind,
		// then authentificate with the server.
		if ($this->ldap_bind_dn) {
			if (!@ldap_bind($conn, $this->ldap_bind_dn, $this->ldap_bind_pwd)) {
				error_log("LDAP application bind failed.");
				return false;
			}
		}

		$this->ldap_conn = $conn;
		return true;
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
