<?php

// fill database with random data

require_once('teams.php');
require_once('dbconn.php');

function generate_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000,
		mt_rand( 0, 0x3fff ) | 0x8000,
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

$now = time();
$bad_reasons = array('overwork', 'underwork', 'customer', 'my-team', 'other-team', 'other-dept', 'project', 'personal');
$good_reasons = array('customer', 'my-team', 'other-team', 'other-dept', 'project', 'personal');

foreach ($teams as $team) {
	foreach ($team['members'] as $member) {
		
		// insert random crap
		$person_uuid = generate_uuid();
		
		// insert a comment?
		if (mt_rand(0, 100) > 50) { // 50% chance
			$comment_db = "'Random comment here.'";
		} else {
			$comment_db = 'null';
		}
		
		$random_rating = mt_rand(1, 5);
		
		$insert_rating = $mysqli->query("INSERT INTO thefeels (uuid, username, therating, thecomment, sent_ts, rated_ts) VALUES ('".$person_uuid."', '$member', $random_rating, ".$comment_db.", $now, $now)");
		
		$reason_chance = mt_rand(0, 100);
		
		if ($random_rating < 3 && $reason_chance > 60) {
			// ok, give em some bad reasons
			$bad_reason_keys = array_rand($bad_reasons, mt_rand(1, 3));
			foreach ($bad_reason_keys as $bad_reason_key) {
				$the_reason = $bad_reasons[$bad_reason_key];
				$save_reason = $mysqli->query("INSERT INTO answers (uuid, therating, thereason, saved_ts) VALUES ('".$person_uuid."', $random_rating, '".$mysqli->escape_string($the_reason)."', $now)");
			}
		} else if ($random_rating > 3 && $reason_chance > 60) {
			// ok, give em some good reasons
			$good_reason_keys = array_rand($good_reasons, mt_rand(1, 3));
			foreach ($good_reason_keys as $good_reason_key) {
				$the_reason = $good_reasons[$good_reason_key];
				$save_reason = $mysqli->query("INSERT INTO answers (uuid, therating, thereason, saved_ts) VALUES ('".$person_uuid."', $random_rating, '".$mysqli->escape_string($the_reason)."', $now)");
			}
		}
		
	}
}

echo 'done';