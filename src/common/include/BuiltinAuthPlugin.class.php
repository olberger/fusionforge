<?php
/**
 * FusionForge authentication management
 *
 * Copyright 2011, Roland Mas
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

abstract class BuiltinAuthPlugin extends AuthPlugin {
	/**
	 * BuiltinAuthPlugin() - constructor
	 *
	 */	
	function BuiltinAuthPlugin() {
		$this->AuthPlugin();
		$this->_addHook('check_auth_session');
		$this->_addHook('fetch_authenticated_user');
		$this->_addHook('display_auth_form');
		// display_create_user_form - display a form to create a user from external auth
		// fetch_account_info - sync identity from external source (realname, email, etc.)
		// get_extra_roles - add new roles not necessarily stored in the database
		// restrict_roles - filter out unwanted roles
		$this->_addHook('close_auth_session');
	}
	
	function displayAuthForm($params) {
		$return_to = $params['return_to'];
		$loginname = '';

		$this->_displayAuthForm($return_to, $login_name);
	}

	function _displayAuthForm($return_to, $login_name) {
		if (session_issecure()) {
			$login_button = _('Login with SSL');
		} else {
			$login_button = _('Login'); 
		}

		echo '<form action="' . util_make_url('/plugins/builtinauth/postlogin.php'); . '" method="post">
<input type="hidden" name="form_key" value="' . form_generate_key() . '"/>
<input type="hidden" name="return_to" value="' . htmlspecialchars(stripslashes($return_to)) . '" />
<p>';
		if (forge_get_config('require_unique_email')) {
			echo _('Login name or email address');
		} else {
			echo _('Login name:');
		}
		echo '<br /><input type="text" name="form_loginname" value="' . htmlspecialchars(stripslashes($login_name)) . '" /></p><p>' . _('Password:') . '<br /><input type="password" name="form_pw" /></p><p><input type="submit" name="login" value="' . $login_button . '" />
</p>
</form>' ;
	}

}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
