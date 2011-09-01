<?php
/** External authentication via WebID for FusionForge
 * Copyright 2011, Roland Mas
 * Copyright 2011, Olivier Berger & Institut Telecom
 *
 * This program was developped in the frame of the COCLICO project
 * (http://www.coclico-project.org/) with financial support of the Paris
 * Region council.
 *
 * This file is part of FusionForge. FusionForge is free software;
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or (at your option)
 * any later version.
 *
 * FusionForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with FusionForge; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// FIXME : WTF ?!?!?!?
Header( "Expires: Wed, 11 Nov 1998 11:11:11 GMT");
Header( "Cache-Control: no-cache");
Header( "Cache-Control: must-revalidate");

require_once('../../../www/env.inc.php');
require_once $gfcommon.'include/pre.php';
require_once('../../../www/include/login-form.php');

// WebID framework
require_once('libAuthentication/lib/Authentication.php');

$plugin = plugin_get_object('authwebid');

$return_to = getStringFromRequest('return_to');
//$login = getStringFromRequest('login');

//$webid_identifier = getStringFromRequest('webid');
$triggered = getIntFromRequest('triggered');

if (forge_get_config('use_ssl') && !session_issecure()) {
	//force use of SSL for login
	// redirect
	session_redirect_external('https://'.getStringFromServer('HTTP_HOST').getStringFromServer('REQUEST_URI'));
}

//try {

	// TODO check error param in request
	
	// initialize the WebID lib handler which will read the posted args
	$IDPCertificates = array ( 'auth.fcns.eu' =>
"-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyfLj5x7XR+v07NgOCtOc
KJgMkq7p1rEvSQ9jfTFYDcL454wv4QFS6OLnkH6KpV61npj0XYznYycgmNvWw9cD
RdhN+fLW0VKqSRYqNAkvSc1JkmW9JisldX33iTiyhVoEDfviu9pMBImalZ1y14A4
LPHAkV5rZy/fRk7F/gMo29JuLSmPngu4ze/+oHHp1+EiIlhMi8exisQvVhhc9n2C
RWL5eYmG9Qr90C1nJnMygDKraTFj3CxStk0HN5NhNYKe1kNFElny9hLxlpL8D0Ul
VYhfC0gRHc6mTRB3NEfSmkQCWJCR1iV9ZrMFD5fO27w5AkMIN4AULUMNxLed3KmC
1QIDAQAB
-----END PUBLIC KEY-----
",
/* "-----BEGIN CERTIFICATE-----
MIIElDCCA3ygAwIBAgILAQAAAAABLyznhtwwDQYJKoZIhvcNAQEFBQAwNjERMA8G
A1UECxMIQWxwaGEgQ0ExDjAMBgNVBAoTBUFscGhhMREwDwYDVQQDEwhBbHBoYSBD
QTAeFw0xMTA0MDYyMTMxMzNaFw0xMjA0MDYyMTMxMzBaMFgxCzAJBgNVBAYTAkZS
MSEwHwYDVQQLExhEb21haW4gQ29udHJvbCBWYWxpZGF0ZWQxEjAQBgNVBAoMCSou
ZmNucy5ldTESMBAGA1UEAwwJKi5mY25zLmV1MIIBIjANBgkqhkiG9w0BAQEFAAOC
AQ8AMIIBCgKCAQEAyfLj5x7XR+v07NgOCtOcKJgMkq7p1rEvSQ9jfTFYDcL454wv
4QFS6OLnkH6KpV61npj0XYznYycgmNvWw9cDRdhN+fLW0VKqSRYqNAkvSc1JkmW9
JisldX33iTiyhVoEDfviu9pMBImalZ1y14A4LPHAkV5rZy/fRk7F/gMo29JuLSmP
ngu4ze/+oHHp1+EiIlhMi8exisQvVhhc9n2CRWL5eYmG9Qr90C1nJnMygDKraTFj
3CxStk0HN5NhNYKe1kNFElny9hLxlpL8D0UlVYhfC0gRHc6mTRB3NEfSmkQCWJCR
1iV9ZrMFD5fO27w5AkMIN4AULUMNxLed3KmC1QIDAQABo4IBfzCCAXswHwYDVR0j
BBgwFoAUCin6ra9N/f1dfXYmh6uqWqp0IhUwTQYIKwYBBQUHAQEEQTA/MD0GCCsG
AQUFBzAChjFodHRwOi8vc2VjdXJlLmFscGhhc3NsLmNvbS9jYWNlcnQvQWxwaGFT
U0xkdjEuY3J0MDIGA1UdHwQrMCkwJ6AloCOGIWh0dHA6Ly9jcmwuYWxwaGFzc2wu
Y29tL0FscGhhLmNybDAdBgNVHQ4EFgQU/zLAtIZbTa0IkyiX7Ca95AFp3iowCQYD
VR0TBAIwADAOBgNVHQ8BAf8EBAMCBaAwHQYDVR0lBBYwFAYIKwYBBQUHAwEGCCsG
AQUFBwMCMEoGA1UdIARDMEEwPwYKKwYBBAGgMgEKCjAxMC8GCCsGAQUFBwIBFiNo
dHRwOi8vd3d3LmFscGhhc3NsLmNvbS9yZXBvc2l0b3J5LzARBglghkgBhvhCAQEE
BAMCBsAwHQYDVR0RBBYwFIIJKi5mY25zLmV1ggdmY25zLmV1MA0GCSqGSIb3DQEB
BQUAA4IBAQCYins7oSJi8ULUuo+eoAqhNPzPPweTqfwCW1dshvkyE7CjZnWmzDXR
XPgFhpwfXCiZ5Nh76mSuQ4sxZvoEJhh3Q3wXOhRkBNrDlUWoWGftfruwji8RgGkJ
piNGsdrBkjFucGgwPE79dya6wMgWmw2XvRnft/JTs2e3Epvm99B0trszb9AYk6sx
rkCGu+VOfWZIu0uu63rrWWMVLQi5OAFBbuczOLYXE/76PpMYbiKORGr8gXzmZ/zq
y24vK4vwFIrWXgldC6FkiI2RBL5to4bWlD/bPjOwpFMfiOOqCALs5zBr/ijIAib3
sbT0swwb2fUto/0Nl7hw9oyQpckoilKX
-----END CERTIFICATE-----
", */
'foafssl.org' =>
"-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhFboiwS5HzsQAAerGOj8
Zk6qvEf2QVarlm+c1fxd6f3OoQ9ezib1LjXitw+z2xcLG8lzaTmKOU0jw7KZp6WL
W6gqhAWj2BQ1Lkl9R7aAUpA3ypk52gik8u/5JiWpTt1EV99DP5XNzzQ/QVjkvBlj
rY+1ZeM+XtKzGfbK7eWh583xn3AE6maprXfLAo3BjUWJOQe0VHGYgrBVOcRQrSQ6
34/f+jk22tmYZRzdTT/ZCadeLd7NryIeJbEu0W105JYvKodawSM3/zjt4fXFIPyB
z8vHHmHRd2syDWqUy46YVQfqCfUBdXkHbvVQBtAfvRGUhYbFQm926an6z9uRE5LC
aQIDAQAB
-----END PUBLIC KEY-----
");
	$certRepository = new Authentication_X509CertRepo($IDPCertificates);

	$plugin->webid = new Authentication_FoafSSLDelegate(FALSE, NULL, NULL, $certRepository);
	/*
	// check the 'webid_mode' that may be set on returning from WebID provider
	if(!$plugin->webid->mode) {

		// We're just called by the login form : redirect to the WebID provider
        if(isset($_POST['webid_identifier'])) {
        	$webid_identifier = $_POST['webid_identifier'];
        	if($plugin->getUserNameFromWebIDIdentity($webid_identifier)) {
            	$plugin->webid->identity = $webid_identifier;
            	session_redirect_external($plugin->webid->authUrl());
        	}
        	else {
        		$warning_msg = _('No such WebID identity registered yet');
        	}
        }

    // or we are called back by the WebID provider
    } elseif($plugin->webid->mode == 'cancel') {
        $warning_msg .= _('User has canceled authentication');
    } else {

    	// Authentication should have been attempted by WebID provider
    	// if ($plugin->webid->validate()) {*/
    	if ( $plugin->webid->isAuthenticated() ) {
    		// If user successfully logged in to WebID provider
		echo "authenticated as :";
		print_r($plugin->webid);
		exit(0);
    		// initiate session
	    	if ($plugin->isSufficient()) {
	    		$user = False;

	    		$username = $plugin->getUserNameFromWebIDIdentity($plugin->webid->webid);
				if ($username) {
					$user_tmp = user_get_object_by_name($username);
					if($user_tmp->usesPlugin($plugin->name)) {
						$user = $plugin->startSession($username);
					}
					else {
						$warning_msg = _('WebID plugin not activated for the user account');
					}
				}

				if($user) {
					// redirect to the proper place in the forge
					if ($return_to) {
						validate_return_to($return_to);

						session_redirect($return_to);
					} else {
						session_redirect("/my");
					}
				}
				else {
					$warning_msg = sprintf (_("Unknown user with identity '%s'"),$plugin->webid->webid);
				}
	    	}
		}
		else {
			echo "error :". $plugin->webid->authnDiagnostic;
			print_r($plugin->webid);
			exit(0);
		}
    //}

	// Otherwise, display the login form again
	display_login_page($return_to, $triggered);
/*
} catch(ErrorException $e) {
    echo 'WebID error'. $e->getMessage();
}*/
	
// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
