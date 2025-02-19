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
 * TODO describe file newteacher
 *
 * @package    local_ourteacher
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_login();

$url = new moodle_url('/local/ourteacher/newteacher.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);

$mform = new local_ourteacher\newteacher();

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
            $usercontext = context_user::instance($userid);
            $user->imagefile = $data->userpicture;
            $user->deletepicture = true;
            $filemanageroptions = [
                'maxbytes'       => $CFG->maxbytes,
                'subdirs'        => 0,
                'maxfiles'       => 1,
                'accepted_types' => 'optimised_image'
            ];

            $draftitemid = file_get_submitted_draft_itemid('userpic');

            file_prepare_draft_area($draftitemid, $usercontext->id, 'user', 'newicon', 0, $filemanageroptions);

            $user->picture = $data->userpicture;
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
        $record->userpic = $data->userpicture;
        $record->roleid = 4;
        file_save_draft_area_files(
            $data->userpicture,
            $usercontext->id,
            'local_ourteacher',
            'userpicture',
            $data->userpicture
        );
        $teacherrecord = $DB->insert_record('teachers', $record);
        if ($teacherrecord) {
            echo " Teacher record Added successfully";
        } else {
            echo "Oops! teacher record not add";
        }
    }



    $roleid = 3; // Replace with the desired role ID

}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
