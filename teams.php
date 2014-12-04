<?php

/*

	fill out your team list!
	
	there can be as many teams as you want
	
	each team has a certain syntax, like the examples below
	
	$teams[] = array( 'name' => 'team name here', 'supervisor' => 'username', 'members' => array( 'email@email.com', 'another@email.com' ) );
	
	the 'supervisor' value will be able to see the members' ratings
	so that should be a valid username
	
	the 'members' array will be the ones sent the emails
	so they should be valid email addresses
	
*/

$teams = array(
	array(
		'name' => 'Test Team',
		'supervisor' => 'george',
		'members' => array('michael@whatever.com', 'hana@whatever.com', 'cyle@whatever.com')
	),
	array(
		'name' => 'Team Two',
		'supervisor' => 'michael',
		'members' => array('doug@whatever.com', 'jake@whatever.com', 'paul@whatever.com', 'carl@whatever.com')
	)
);