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
 * TODO describe file delete
 *
 * @package    mod_newexternship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$dataid = required_param('dataid',PARAM_INT);
$confirmed = optional_param('confirmed', false, PARAM_BOOL);
require_login();

$url = new moodle_url('/mod/newexternship/delete.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);

$datarecord = $DB->get_record('newexternship_data',['id'=>$dataid]);
$cm =$DB->get_record('course_modules',['id'=>$datarecord->cmid]);
$newexternship =$DB->get_record('newexternship',['id'=>$datarecord->newexternshipid]);
if ($confirmed && $dataid && $cm && $newexternship) {
    // Create the event for deleting an externship
    $event = \mod_newexternship\event\newexternship_delete::create(array(
        'objectid' => $dataid, // The ID of the externship being deleted
        'context'  => \context_module::instance($cm->id), // The context of the course module
        'other'    => array('newexternship_name' => $newexternship->name) // Additional data
    ));

    // Trigger the event
    $event->trigger();
}


if ($confirmed) {
    if ($DB->delete_records('newexternship_data', array('id'=> $dataid))){
        $link =$CFG->wwwroot.'/mod/newexternship/view.php?id='.$cm->id;
        notice (get_string("eventnewexternshipdeleted", 'newexternship'),$link);
    }

}

if (!isset($msg_data) || !is_object($msg_data)) {
    $msg_data = new stdClass();
}

echo $OUTPUT->confirm (
                get_string("confirmnewexternshipdeleted", 'newexternship', $msg_data),
                "delete.php?confirmed=true&dataid=$dataid",
                $CFG->wwwroot.'/mod/newexternship/view.php?id='.$cm->id
);
echo $OUTPUT->header();

echo $OUTPUT->footer();
