<?php
	
//echo '<pre>'.print_r($_REQUEST, true).'</pre>';

if (!isset($_REQUEST['rating']) || trim($_REQUEST['rating']) == '' || !is_numeric($_REQUEST['rating'])) {
	die('It looks like you forgot to provide a rating, please go back and try again.');
}

if (!isset($_REQUEST['uuid']) || trim($_REQUEST['uuid']) == '') {
	die('It looks like you did not provide a unique ID, please try again.');
}

require_once('dbconn.php');

$uuid = trim($_REQUEST['uuid']);
$rating = (int) $_REQUEST['rating'] * 1;
$now = time();

/*

	ok now save answers, if there are any

*/

if (isset($_REQUEST['reason']) && is_array($_REQUEST['reason'])) {
	// clear old answers if there are any
	$clear_reasons = $mysqli->query("DELETE FROM answers WHERE uuid='".$mysqli->escape_string($uuid)."'");
	if (!$clear_reasons) {
		die('There was an error clearing your old reasons: '.$mysqli->error);
	}
	foreach ($_REQUEST['reason'] as $reason) {
		$save_reason = $mysqli->query("INSERT INTO answers (uuid, therating, thereason, saved_ts) VALUES ('".$mysqli->escape_string($uuid)."', $rating, '".$mysqli->escape_string($reason)."', $now)");
		if (!$save_reason) {
			die('There was an error saving the reason: '.$mysqli->error);
		}
	}
}

// save comment, if there is any
if (isset($_REQUEST['comment']) && trim($_REQUEST['comment']) != '') {
	$save_comment = $mysqli->query("UPDATE thefeels SET thecomment='".$mysqli->escape_string(trim($_REQUEST['comment']))."' WHERE uuid='".$mysqli->escape_string($uuid)."'");
	if (!$save_comment) {
		die('There was an error saving your comment: '.$mysqli->error);
	}
}

header('Location: ./?thanks');