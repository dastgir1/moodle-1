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
 * TODO describe file comments_slider
 *
 * @package    local_comments_slider
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/comments_slider/comments_slider.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$records = $DB->get_records_sql("
    SELECT c.id AS i, c.content AS comment_content,
           u.id, u.firstname, u.lastname, ra.roleid
    FROM {comments} c
    JOIN {user} u ON c.userid = u.id
    JOIN {role_assignments} ra ON c.userid = ra.userid AND c.contextid = ra.contextid
");

$combinedArray = [];
$tempArray = [];
$counter = 0;
$firstSlide = true;

foreach ($records as $record) {
    $userrole = $DB->get_record_sql("SELECT shortname FROM {role} WHERE id = ?", [$record->roleid]);
    $record->role = $userrole->shortname;
    $record->picture = $OUTPUT->user_picture(core_user::get_user($record->id));
    $usercontext = context_user::instance($record->id);

    $fs = get_file_storage();
    $files = $fs->get_area_files($usercontext->id, 'user', 'icon', $record->picture);
    $file = end($files);
    if ($file) {
        // Creating picture URL.
        $record->picurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename(),
            false
        )->out();
    } else {
        // Default picture URL or handle missing picture.
        $record->picurl = $CFG->wwwroot . '/pix/u/f1.png';
    }

    $tempArray[] = $record;
    $counter++;

    if ($counter == 1) {
        $combinedArray[] = ['users' => $tempArray, 'isFirst' => $firstSlide];
        $tempArray = [];
        $counter = 0;
        $firstSlide = false;
    }
}

// If there are remaining users, add them as the last slide.
if (!empty($tempArray)) {
    $combinedArray[] = ['users' => $tempArray, 'isFirst' => $firstSlide];
}

echo $OUTPUT->render_from_template('local_comments_slider/comments_slider', ['slides' => $combinedArray]);

echo $OUTPUT->footer();
