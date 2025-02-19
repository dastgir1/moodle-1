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
 * TODO describe file testimonial
 *
 * @package    local_ourteacher
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/ourteacher/testimonial.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$allcomments = $DB->get_records_sql("
    SELECT 
        c.id AS commentid, 
        c.content AS comment_content, 
        u.id AS userid, 
        u.firstname, 
        u.lastname, 
        ra.roleid,
        u.picture
    FROM {comments} c
    JOIN {user} u ON c.userid = u.id
    JOIN {role_assignments} ra ON c.userid = ra.userid AND c.contextid=ra.contextid
");
$combinedArray = [];
$tempArray = [];
$counter = 0;
foreach ($allcomments as $comment) {

    $user = $DB->get_record('user', ['id' => $comment->userid]);
    $userrole = $DB->get_record('role', ['id' => $comment->roleid]);
    $tempArray[] = [
        'userid' => $comment->userid,
        'comment_content' => $comment->comment_content,
        'firstname' => $comment->firstname,
        'lastname' => $comment->lastname,
        'picture' => $OUTPUT->user_picture($user, ['size' => 100]),
        'role' => $userrole->shortname ?? 'No role',
    ];

    $counter++;

    // When we have 3 records, add to the combined array and reset.
    if ($counter == 3) {
        $combinedArray[] = ['users' => $tempArray];
        $tempArray = [];
        $counter = 0;
    }
}
// Prepare data for rendering.
echo $OUTPUT->render_from_template('local_ourteacher/testimonial', ['slides' => $combinedArray, 'users' => $tempArray]);
echo $OUTPUT->footer();
