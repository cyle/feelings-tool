<?php

/*

	LOGIN CHECKING
	
	I do this via SAML where this is currently implemented,
	you'll need to write your own auth here!

*/

$login_required = true; // for this app -- always require login

// set defaults for your user object
$current_user = array(
	'loggedin' => false,
	'username' => 'nobody'
);

if (isset($login_required) && $login_required == true) {
	
	// you'll need to do something in here to set
	// $current_user['loggedin'] = true;
	// and
	// $current_user['username'] = "somebody";
	// and kick out anybody who's not authenticated properly
	
}