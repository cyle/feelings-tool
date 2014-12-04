# The Feelings Tool

We work in a time when you should not only be collecting metrics-driven feedback from your constituents and customers, but also feedback from the people you work with. It's not only about how much your customers love you, it's also about how your team loves each other.

To that end, I've created the "Feelings Tool", which sends an email blast at the end of every week, polling everyone in the department how they feel on a scale from terrible to awesome. These moods are represented with friendly emoji. Each answer is recorded and can be re-submitted. Optionally, a user can also answer a quick question about _why_ they feel that way. The goal is to allow team leaders to see how their teams are doing and why they may be doing really well or really poorly.

## Installation/Requirements

You need PHP5.4+, the mysqli extension, a MySQL database, the Mail and Mail_Mime PEAR libs, and some kind of authentication system (which you'll have to write into `login_check.php`).

To install the Mail and Mail_mime libs:

    pear install Mail Mail_Mime Auth_SASL Net_SMTP

You'll also need to specify your team's hierarchy and email addresses in the `teams.php` file.

You'll also need to open up the `template.html` file and customize the URL path to all of the images and the link in the body text.

You'll also need to open up `send-email.php` and fill in some variables about your base URL, an SMTP server to send through, and what the "From" field will be set to in the emails.

To install the database, use `create-feels-db.sql`, rename `dbconn.sample.php` to `dbconn.php` and edit it as needed.

To make sure the emails get sent out, set up a CRON job to execute the `send-email.php`, like so:

    30 9 * * 5 /usr/bin/php /path/to/feelings/send-email.php

That'll send out the emails at 9:30 AM on Friday morning.

## Usage

Once enough people start clicking on their week's rating in the email, you'll begin to see the aggregate results. If you're a team leader (specified in `teams.php`), you'll see your team's answers.

Edit the `template.html` file to adjust the HTML email that gets sent out.

The ratings are served via unique identifiers that are tied to each individual email, so they should not be forwarded around.

If you just want to see what it might look like, you can use the `random_data.php` script to fill the database will random test data.

## To-dos

A graph over time of average ratings, both aggregated department-wide and per-team.