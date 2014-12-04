<?php

/*

	WEEKLY FEELINGS TOOL

 */

function rating_to_smiley($rating_number, $small = true) {
	$rating_number = $rating_number * 1;
	if ($small) {
		$style = 'width:24px;height:24px;';
	} else {
		$style = '';
	}
	if ($rating_number <= 1.5) {
		return '<img src="images/persevere.png" style="'.$style.'" />';
	} else if ($rating_number > 1.5 && $rating_number <= 2.5) {
		return '<img src="images/disappointed.png" style="'.$style.'" />';
	} else if ($rating_number > 2.5 && $rating_number <= 3.5) {
		return '<img src="images/neutral_face.png" style="'.$style.'" />';
	} else if ($rating_number > 3.5 && $rating_number <= 4.5) {
		return '<img src="images/relieved.png" style="'.$style.'" />';
	} else if ($rating_number > 4.5) {
		return '<img src="images/smile.png" style="'.$style.'" />';
	}
}

$bad_reasons = array(
	'overwork' => 'Too much work',
	'underwork' => 'Not enough work',
	'customer' => 'Customer interaction went poorly',
	'my-team' => 'My team',
	'other-team' => 'Another team',
	'other-dept' => 'Another department',
	'project' => 'Project struggles',
	'personal' => 'Something personal'
);

$good_reasons = array(
	'customer' => 'Customer interaction went well',
	'my-team' => 'My team',
	'other-team' => 'Another team',
	'other-dept' => 'Another department',
	'project' => 'Project struggles',
	'personal' => 'Something personal'
);

require_once('login_check.php');
require_once('dbconn.php');
require_once('teams.php');

if (date('D') != 'Mon') {
	$week_start = date('Y-m-d', strtotime('last Monday'));
} else {
	$week_start = date('Y-m-d');
}

$week_start_ts = strtotime($week_start . ' 12:00 AM');

if (date('D') != 'Fri') {
    $week_end = date('Y-m-d', strtotime('next Friday'));
} else {
	$week_end = date('Y-m-d');
}

$week_end_ts = strtotime($week_end . ' 11:59 PM');

?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Feelings Tool</title>
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700" rel="stylesheet" type="text/css" />
<link href="feels.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="container">

<div id="header">
<h1>Feelings</h1>
<h2>Week of <?php echo $week_start; ?> to <?php echo $week_end; ?></h2>
</div>

<?php
if (isset($_GET['thanks'])) {
?>
<div id="thanks">Thanks for sharing your thoughts! You can see the current average rating below.</div>
<?php
}
?>

<div id="so-far">
<?php
$get_average_rating_query = "SELECT SUM(therating) AS rating_sum, COUNT(uuid) AS rating_count FROM thefeels WHERE therating IS NOT NULL AND sent_ts > $week_start_ts AND sent_ts < $week_end_ts";
//echo '<p>'.$get_average_rating_query.'</p>';
$get_average_rating = $mysqli->query($get_average_rating_query);
if (!$get_average_rating) {
	die('Database error fetching ratings: '.$mysqli->error);
}
if ($get_average_rating->num_rows == 0) {
	echo '<p>No results to show yet.</p>';
} else {
	$rating_info = $get_average_rating->fetch_assoc();
	if ($rating_info['rating_count'] < 4) {
		echo '<p>Not enough results to show an aggregate yet.</p>';
	} else {
		//print_r($rating_info);
		$average_rating = number_format($rating_info['rating_sum']/$rating_info['rating_count'], 1);
		echo '<p>'.rating_to_smiley($average_rating, false).'</p>';
		echo '<p><b>'.$rating_info['rating_count'].' ratings</b> so far, with an average rating of <b>'.$average_rating.'</b> out of 5.</p>';
		
		// get average reasons why
		$get_bad_reasons = $mysqli->query("SELECT COUNT(uuid) AS thecount, thereason FROM answers WHERE therating < 3 AND saved_ts > $week_start_ts AND saved_ts < $week_end_ts GROUP BY thereason ORDER BY thecount DESC");
		if ($get_bad_reasons->num_rows > 0) {
			echo '<h3>'.rating_to_smiley(2).' Common reasons why people had a bad week:</h3>';
			while ($bad_reason_result = $get_bad_reasons->fetch_assoc()) {
				if ($bad_reason_result['thecount'] < 2) {
					continue;
				}
				echo '<p>'.$bad_reasons[$bad_reason_result['thereason']].' ('.$bad_reason_result['thecount'].')</p>';
			}
		}
		
		// get good reasons, too
		$get_good_reasons = $mysqli->query("SELECT COUNT(uuid) AS thecount, thereason FROM answers WHERE therating > 3 AND saved_ts > $week_start_ts AND saved_ts < $week_end_ts GROUP BY thereason ORDER BY thecount DESC");
		if ($get_good_reasons->num_rows > 0) {
			echo '<h3>'.rating_to_smiley(4).' Common reasons why people had a good week:</h3>';
			while ($good_reason_result = $get_good_reasons->fetch_assoc()) {
				if ($good_reason_result['thecount'] < 2) {
					continue;
				}
				echo '<p>'.$good_reasons[$good_reason_result['thereason']].' ('.$good_reason_result['thecount'].')</p>';
			}
		}
	}
	
	// if you're a supervisor, show your team(s)
	foreach ($teams as $team) {
		if ($current_user['username'] == strtolower($team['supervisor'])) {
			// your team! see the members' ratings and whatnot
			echo '<h2>Your Team: '.$team['name'].'</h2>';
			
			// get average for your team
			$username_list = '';
			foreach ($team['members'] as $member) {
				if ($username_list != '') {
					$username_list .= ', ';
				}
				$username_list .= "'".$mysqli->escape_string($member)."'";
			}
			
			$get_team_average = $mysqli->query("SELECT SUM(therating) AS rating_sum, COUNT(uuid) AS rating_count FROM thefeels WHERE therating IS NOT NULL AND username IN ($username_list) AND sent_ts > $week_start_ts AND sent_ts < $week_end_ts");
			$team_average_result = $get_team_average->fetch_assoc();
			if ($team_average_result['rating_count'] == 0) {
				echo '<p>No one has answered yet, so there is no team average.</p>';
			} else {
				$team_average_rating = number_format($team_average_result['rating_sum']/$team_average_result['rating_count'], 1);
				echo '<p><b>Team average rating: '.$team_average_rating.'/5</b> '.rating_to_smiley($team_average_rating).'</p>';
			}
			
			foreach ($team['members'] as $member) {
				echo '<div class="rating-result">';
				// get their rating and comment for this week (if any)
				$get_person_rating = $mysqli->query("SELECT * FROM thefeels WHERE username='".$mysqli->escape_string($member)."' AND sent_ts > $week_start_ts AND sent_ts < $week_end_ts");
				if ($get_person_rating->num_rows == 1) {
					$person_rating = $get_person_rating->fetch_assoc();
					//echo '<pre>'.print_r($person_rating, true).'</pre>';
					echo '<p><b>'.$member.'</b> rates their week '.$person_rating['therating'].'/5 '.rating_to_smiley($person_rating['therating']).'</p>';
					// also get their answers for why, if any
					$get_person_reasons = $mysqli->query("SELECT * FROM answers WHERE uuid='".$mysqli->escape_string($person_rating['uuid'])."'");
					if ($get_person_reasons->num_rows > 0) {
						while ($person_reason = $get_person_reasons->fetch_assoc()) {
							echo '<p>Reason why: ';
							if ($person_reason['therating'] > 3) {
								echo $good_reasons[$person_reason['thereason']];
							} else if ($person_reason['therating'] < 3) {
								echo $bad_reasons[$person_reason['thereason']];
							}
							echo '</p>';
						}
					} else {
						echo '<p>They gave no reason why.</p>';
					}
					// comment?
					if (isset($person_rating['thecomment']) && trim($person_rating['thecomment']) != '') {
						echo '<p>Comment: '.$person_rating['thecomment'].'</p>';
					}
				} else {
					echo '<p><b>'.$member.'</b> has not responded this week (yet).</p>';
				}
				echo '</div>';
			}
		}
	}
	
}
?>
</div>

</div>

</body>
</html>