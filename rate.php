<?php

// save the rating for the corresponding UUID

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

$update = $mysqli->query("UPDATE thefeels SET therating=$rating, rated_ts=$now WHERE uuid='".$mysqli->escape_string($uuid)."'");
if ($update == false) {
	die('Database error saving your rating: '.$mysqli->error);
}

?><!doctype html>
<html>
<head>
<title>Feelings Tool</title>
<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700" />
<link rel="stylesheet" href="feels.css" />
</head>
<body>
<div class="container">
<h1>Thank you for your rating!</h1>
<p>Your contribution will go towards achieving a better understanding of how your team feels and how all of us feel this week!</p>
<?php if ($rating != 3) { ?><p>You can do even more by answering an additional question. <b>This is entirely optional.</b></p><?php } ?>
<p>Your answers will be directly visible to only your supervisor, and also will be aggregated into a department-wide average.</p>
<form action="save_answers.php" method="post">
<input type="hidden" name="uuid" value="<?php echo $uuid; ?>" />
<input type="hidden" name="rating" value="<?php echo $rating; ?>" />
<?php
if ($rating < 3) { // why so terrible?
?>
<p>Why did you rate your week as not-so-great? Select as many as you'd like.</p>
<p><label><input type="checkbox" name="reason[]" value="overwork" /> Too much to do</label></p>
<p><label><input type="checkbox" name="reason[]" value="underwork" /> Not enough to do</label></p>
<p><label><input type="checkbox" name="reason[]" value="customer" /> Customer interaction went poorly</label></p>
<p><label><input type="checkbox" name="reason[]" value="my-team" /> My team</label></p>
<p><label><input type="checkbox" name="reason[]" value="other-team" /> Another IT team</label></p>
<p><label><input type="checkbox" name="reason[]" value="other-dept" /> Another department</label></p>
<p><label><input type="checkbox" name="reason[]" value="project" /> Project struggles</label></p>
<p><label><input type="checkbox" name="reason[]" value="personal" /> Something personal</label></p>
<?php
} else if ($rating > 3) { // why so great?
?>
<p>Why did you rate your week as okay or better? Select as many as you'd like.</p>
<p><label><input type="checkbox" name="reason[]" value="customer" /> Customer interaction went well</label></p>
<p><label><input type="checkbox" name="reason[]" value="my-team" /> My team</label></p>
<p><label><input type="checkbox" name="reason[]" value="other-team" /> Another IT team</label></p>
<p><label><input type="checkbox" name="reason[]" value="other-dept" /> Another department</label></p>
<p><label><input type="checkbox" name="reason[]" value="project" /> Project success</label></p>
<p><label><input type="checkbox" name="reason[]" value="personal" /> Something personal</label></p>
<?php
}
?>
<p>If you'd like to also leave a comment or question for your supervisor about your week, please do so here:</p>
<p><textarea class="comment" name="comment"></textarea></p>
<p><input type="submit" value="Submit &raquo;" /></p>
</form>
</div>
</body>
</html>