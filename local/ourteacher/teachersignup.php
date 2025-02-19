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
 * Form for editing our teacher  instances.
 *  @copyright  2023 YOUR NAME <your@email.com>
 * @package  local_ourteacher
 * @author    paktaleem
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/page/locallib.php');
require_once($CFG->dirroot . '/local/ourteacher/lib.php');
require_once($CFG->dirroot . '/local/ourteacher/classes/teachersignup.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/enrol/manual/locallib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');

$path = optional_param('path', '', PARAM_PATH); // $nameofarray = optional_param_array('nameofarray', null, PARAM_INT);
$pageparams = array();

if ($path) {
    $pageparams['path'] = $path;
}

global $CFG, $USER, $DB, $OUTPUT, $PAGE, $instance;

$PAGE->set_url('/local/ourteacher/teachersignup.php');

require_login();

$PAGE->set_pagelayout('admin');
$context = context_system::instance();
$PAGE->set_context($context);

$header = $SITE->fullname;
$PAGE->set_title(get_string('pluginname', 'local_ourteacher'));
$PAGE->set_heading($header);

$mform = new local_ourteacher\teachersignup();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/ourteacher/ourteacher.php'));
} else if ($mform->is_submitted()) {
    if ($data = $mform->get_data()) {

        //  Get teacher data and signup in as ateacher
        $user = new stdClass();
        $user->email = $data->email;
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->description = $data->qualification;
        $user->username =  $user->email;
        $password = random_string(8);
        $user->password = hash_internal_user_password($password);
        $table = "user";
        $conditions = ["email" => $user->email];
        $result = $DB->get_record(
            $table,
            $conditions,
            $fields = '*',
            $strictness = IGNORE_MISSING
        );
        if (!empty($result)) {
            redirect($CFG->wwwroot . "/login/index.php");
        } else {

            $user->auth       = 'manual';
            $user->confirmed  = 1;
            $user->mnethostid = 1;
            $user->lastlogin = time();
            $user->currentlogin = time();
            $user->country    = 'ES'; //Or another country
            $user->lang       = 'en'; //Or another country
            $user->timecreated = time();
            $user->maildisplay = 0;
            $user->mailformat = 1;
            // [START] Code added by S.Zonair.
            $userid = user_create_user($user);
            if ($userid) {
                $toUser = \core_user::get_user($userid);
                $fromUser = \core_user::get_support_user();
                $site = get_site();
                $subject = "Welcome to {$site->fullname}";
                $messagehtml = "Dear {$user->firstname},<br><br>";
                $messagehtml .= "Welcome to {$site->fullname}! We are excited to have you with us. Your username is <strong>{$user->username}</strong>. Click <a href='{$CFG->wwwroot}'>here</a> to login.<br><br>";
                $messagehtml .= "Best regards,<br>";
                $messagehtml .= "{$site->fullname} team";

                $messagetext = html_to_text($messagehtml);

                // $mailSent = email_to_user($user, $supportuser, $subject, $messagetext, $messagehtml);
                $mailSent = email_to_user($toUser, $fromUser, $subject, $messagetext, $messagehtml, '', '', false);

                // Check if the email was sent successfully
                if ($mailSent) {
                    echo 'Email sent successfully.';
                } else {
                    echo 'Failed to send email.';
                }
            }

            $usercontext = context_user::instance($userid);
            $user->imagefile = $data->userpic;
            $user->deletepicture = true;
            $filemanageroptions = [
                'maxbytes'       => $CFG->maxbytes,
                'subdirs'        => 0,
                'maxfiles'       => 1,
                'accepted_types' => 'optimised_image'
            ];

            $draftitemid = file_get_submitted_draft_itemid('userpic');

            file_prepare_draft_area($draftitemid, $usercontext->id, 'user', 'newicon', 0, $filemanageroptions);

            $user->picture = $data->userpic;
            $user->id =  $userid;

            if (core_user::update_picture($user, $filemanageroptions)) {
                echo "user picture update successfuly.";
            } else {
                echo "user picture not update.";
            }

            // [END] Code added by S.Zonair.         
        }
        //  Get teacher data and signup in as ateacher
        $record = new stdClass();

        $record->userid =  $userid;
        $record->qualification = $data->qualification;
        $record->userpic = $data->userpic;
        file_save_draft_area_files(
            $data->userpic,
            $context->id,
            'local_ourteacher',
            'userpic',
            $data->userpic
        );
        $teacherrecord = $DB->insert_record('teachers', $record);
        if ($teacherrecord) {
            echo " Teacher record Added successfully";
        } else {
            echo "Oops! teacher record not add";
        }
    }


    // Define the course data
    $course = new stdClass();
    $course->fullname = $user->firstname . " " . $user->lastname;
    $course->shortname = $user->firstname;
    $course->category = 1; // Replace with the desired category ID
    $course->summary = $user->description;
    $course->format = 'topics'; // You can choose other course formats
    $course->visible = 1; // Set to 1 to make the course visible
    // Create the course
    $courseid = create_course($course);
    $context = context_course::instance($course->id);
    $filearea = 'overviewfiles';
    $draftitemid = file_get_submitted_draft_itemid('userpic');
    $fileinfo = array('contextid' => $context->id, 'component' => 'course', 'filearea' => $filearea, 'itemid' => $draftitemid,);
    // Save the uploaded file
    file_save_draft_area_files($fileinfo, $context->id, 'course', $filearea, 0, ['subdirs' => false]);
    file_prepare_draft_area($draftitemid, $context->id, 'course', 'overviewfiles', 0, ['subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,]);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);
    $file = end($files);
    if (isset($file)) {
        $course->image = $data->userpic;
        update_course($course);
    } else {
        // File upload failed
        // Handle the error
    }
    // Display success or error message
    if ($courseid) {

        echo "Course created successfully with ID: $course->id";
    } else {
        echo "Failed to create course.";
    }

    $roleid = 3; // Replace with the desired role ID
    if (!is_enrolled(context_course::instance($course->id), $user->id, '', true)) {
        // Enroll the user manually
        $enrol = enrol_get_plugin('manual');
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $instance) {
            if ($instance->enrol == 'manual') {
                $enrolmanual = new enrol_manual_plugin();
                $enrolmanual->enrol_user($instance, $user->id, $roleid);
                break; // Assuming there is only one manual enrollment instance
            }
        }
        // Optionally, you can notify the user or perform additional actions upon successful enrollment
        notice(
            "User enrolled successfully in the course",
            new moodle_url('/course/view.php', array('id' => $course->id))
        );
    } else {
        // The user is already enrolled in the course
        echo "User is already enrolled in the course.";
    }
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
