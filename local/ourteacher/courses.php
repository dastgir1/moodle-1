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
 * TODO describe file courses
 *
 * @package    local_ourteacher
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();
$PAGE->requires->css('/local/ourteacher/lib/animate/animate.min.css');
$PAGE->requires->css('/local/ourteacher/lib/animate/animate.css');
$PAGE->requires->css('/local/ourteacher/amd/css/style.css');
$PAGE->requires->js('/local/ourteacher/wow/wow.min.js');
$PAGE->requires->js('/local/ourteacher/lib/easing/easing.min.js');
$PAGE->requires->js('/local/ourteacher/lib/waypoints/waypoints.min.js');
$PAGE->requires->js('/local/ourteacher/amd/js/main.js');
$url = new moodle_url('/local/ourteacher/courses.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$courses = $DB->get_records_sql("SELECT * FROM {course} WHERE id !=1");
$data = [];
foreach ($courses as $course) {
    $enroles = $DB->get_record('enrol', ['courseid' => $course->id, 'name' => 'stripe']);

    // Get course context.
    $ccontext = context_course::instance($course->id);

    // Initialize file storage.
    $fs = get_file_storage();

    // Retrieve files in the 'overviewfiles' file area for the course.
    $files = $fs->get_area_files($ccontext->id, 'course', 'overviewfiles', 0, 'sortorder', false);

    // Get the first valid file (if any exist).
    $file = reset($files);

    $isimage = $file->is_valid_image();
    $imgurl = file_encode_url(
        "$CFG->wwwroot/pluginfile.php",
        '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
            $file->get_filearea() . $file->get_filepath() . $file->get_filename(),
        !$isimage
    );
    $course->image = $imgurl;
    $course->cost = $enroles->cost;
    $course->currency = $enroles->currency;
    $course->startdate = date('m/d/Y', $course->startdate);
    if (is_siteadmin()) {

        $link = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
    } else {
        $link = $CFG->wwwroot . '/course/info.php?id=' . $course->id;
    }
    $course->link = $link;
    $count = $DB->get_field_sql(
        "SELECT COUNT(DISTINCT ue.userid) 
     FROM {user_enrolments} ue
     JOIN {enrol} e ON ue.enrolid = e.id
     JOIN {role_assignments} ra ON ue.userid = ra.userid
     JOIN {context} ctx ON ra.contextid = ctx.id
     JOIN {role} r ON ra.roleid = r.id
     WHERE e.courseid = ? AND ctx.contextlevel = 50 AND r.shortname = 'student'",
        [$course->id]
    );
    $context = context_course::instance($course->id);

    // Get enrolled users with the "editingteacher" role
    $teachers = get_role_users(3, $context);
    foreach ($teachers as $teacher);
    $fullname = $teacher->firstname . ' ' . $teacher->lastname;

    $course->noofstudent = $count;
    $course->teacher = $fullname;

    $data[] = $course;
}
echo $OUTPUT->render_from_template('local_ourteacher/courses', ['courses' => array_values($data)]);
echo $OUTPUT->footer();
