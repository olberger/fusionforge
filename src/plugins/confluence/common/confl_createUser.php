<?php
//
// 
// Create Confluence account
// $0   -a USER  -s url  -u admin -p xxxx
// Ex:
//	xxxx.pl  -a toto -e email -s https://confluence.app.alcatel-lucent.com \
//		 -u admin -p xxxx 
// 
// SOAP description: 
// 	http://confluence.atlassian.com/display/CONFDEV/Remote+API+Specification#RemoteAPISpecification-SOAPInformation
//
// By PhM, 2011-02-25 

// Mandatory args: space and options
$opts = getopt("a:s:u:p:e:");
#var_dump ($opts);
if (! ( isset($opts{'s'}) && isset($opts{'a'}) && isset($opts{'u'}) &&
	isset($opts{'e'}) && isset($opts{'p'}) ))  {
	echo  "Missing options \n"; var_dump ($opts);
	usage($argv[0]);
} ;

// SOAP access point
$base_url = $opts{'s'};
$wsdl = $base_url . '/rpc/soap-axis/confluenceservice-v1?wsdl';

// Credentials 
$username = $opts{'u'}; $password = $opts{'p'};

// Confluence Soap client
$confluence = new SoapClient($wsdl);
try {
  $token = $confluence->login($username, $password);
  
  // Create a user 
  # remoteEntityName is either a group, a user name or null for anonymous 
    #$remoteEN = $confluence->getUser($token,$opts{'a'} );
  $remoteEN = array(
		    'name'     => $opts{'a'},
		    'fullname' => $opts{'a'},
		    'email'    => $opts{'e'},
		    'url'      => NULL);
  var_dump ($remoteEN);
  $confluence->addUser($token, $remoteEN, 'pp6xx8ww');
  $confluence->addUserToGroup($token, $opts{'a'}, 'confluence-users');
}

catch (SoapFault $fault) {
    #echo "Error accessing Confluence";
    print_r($fault);
    exitError ("$argv[0] exiting \n");
}

// Usage
function usage($f) {
 exitError("$f  -a account -e email  -s url  -u admin -p xxxx \n");
};
// Exit on error
function exitError($msg) {
	 die ($msg); #error_log($msg, 0);
}
