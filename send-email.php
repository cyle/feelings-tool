<?php

/*

    send the email for ratings

    each email must have a unique ID that'll be accepted as a unique rating for a unique person

    to a page that'll accept the unique ID and the rating and record it

*/

// EDIT THESE
$base_url = 'https://where-you-hosted.com/feelings/rate.php?uuid=';
$smtp_host = 'smtp-server-to-send-mail-through.com';
$mail_from = 'noreply@whatever.com';
$template_path = '/path/to/this/dir/template.html';
// OK DONE EDITING

require_once('teams.php');

// compile list of people to email
$people_to_email = array();
foreach ($teams as $team) {
	foreach ($team['members'] as $member) {
		$people_to_email[] = $member;
	}
}

$people_to_email = array_unique($people_to_email);

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

$mail_html_template = file_get_contents($template_path);

include('Mail.php');
include('Mail/mime.php');

$mailer = Mail::factory('smtp', array('host' => $smtp_host));

foreach ($people_to_email as $person_to_email) {

    $person_uuid = generate_uuid();

    // save the UUID and the email address into the database
    $save_info = $mysqli->query("INSERT INTO thefeels (uuid, username, sent_ts) VALUES ('$person_uuid', '".$mysqli->escape_string($person_to_email)."', UNIX_TIMESTAMP())");
    if (!$save_info) {
        die('Database error saving UUID: '.$mysqli->error);
    }
    
    $rating_response_url = $base_url.$person_uuid;

    $headers = array();
    $headers['To'] = $person_to_email;
    $headers['From'] = $mail_from;
    $headers['Subject'] = 'How was your work week?';

	// this'll be the HTML copy of the message
	$mail_html_body = str_replace('{{thelink}}', $rating_response_url, $mail_html_template);

    // initialize the MIME part
    $crlf = "\n";
    $mime = new Mail_mime($crlf);

    // set up the body of the email via the MIME library
    $mime->setTXTBody("I'm not sure how to send you this email, since your client does not support HTML emails.");
    $mime->setHTMLBody($mail_html_body);
    $mime_body = $mime->get();
	
	$headers = $mime->headers($headers);
	
    // send it
    $mail_send_result = $mailer->send($email_address, $headers, $mime_body);

    // wat happen?
    if (PEAR::isError($mail_send_result)) {
    	echo '<h1>email not sent! oh no!</h1>';
    	echo '<pre>'.print_r($mail_send_result->getMessage(), true).'</pre>';
    	echo '<pre>'.print_r($mail_send_result, true).'</pre>';
        die();
    }
}

echo 'done!'."\n";
