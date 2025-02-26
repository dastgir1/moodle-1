<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * TODO describe file sendEmail
 *
 * @package    local_contactus
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/contactus/sendEmail.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

$name = required_param('name', PARAM_TEXT);
$subject = required_param('subject', PARAM_TEXT);
$message = required_param('message', PARAM_TEXT);
$to = \core_user::get_support_user();
$from = required_param('email', PARAM_TEXT);
$body = "From:  $from \n username: $name \n Message: $message";

if (email_to_user($to, $from, $subject, $body)) {

    notice("Your Message send to the site Admin", new moodle_url('/local/contactus/contactus.php'));
} else {
    notice("some thing went wrong email not send", new moodle_url('/local/contactus/contactus.php'));
}

echo $OUTPUT->footer();
