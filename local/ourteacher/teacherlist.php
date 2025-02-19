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
 * TODO describe file teacherlist
 *
 * @package    local_ourteacher
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$PAGE->requires->css('/local/ourteacher/lib/animate/animate.min.css');
$PAGE->requires->css('/local/ourteacher/lib/animate/animate.css');
$PAGE->requires->css('/local/ourteacher/amd/css/styles.css');
$PAGE->requires->js('/local/ourteacher/wow/wow.min.js');
$PAGE->requires->js('/local/ourteacher/lib/easing/easing.min.js');
$PAGE->requires->js('/local/ourteacher/lib/waypoints/waypoints.min.js');
$PAGE->requires->js('/local/ourteacher/amd/js/main.js');
require_login();

$url = new moodle_url('/local/ourteacher/teacherlist.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
global $DB;
$teachers = $DB->get_records_sql('SELECT t.userid,t.userpic,t.roleid,u.firstname,u.lastname FROM {teachers} t JOIN {user} u  WHERE u.id= t.userid');
$teacherdata = [];
foreach ($teachers as $teacher) {
    $role = $DB->get_record('role', ['id' => $teacher->roleid]);
    $teacher->role = $role->shortname;
    $users = $DB->get_record('user', ['id' => $teacher->userid]);
    $usercontext = context_user::instance($users->id);

    $fs = get_file_storage();
    $files = $fs->get_area_files($usercontext->id, 'local_ourteacher', 'userpicture', $teacher->userpic);
    $file = end($files);
    if ($file->is_valid_image()) {
        // Creating picture URL.
        $teacher->picurl = moodle_url::make_pluginfile_url(
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
        $teacher->picurl = $CFG->wwwroot . '/pix/u/f1.png';
    }

    $teacherdata[] = $teacher;
}
echo $OUTPUT->render_from_template('local_ourteacher/teacherlist', ['teacherdata' => array_values($teacherdata)]);
echo $OUTPUT->footer();
