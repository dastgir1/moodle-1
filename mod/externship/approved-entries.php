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
 * TODO describe file approved-entries
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$cmid = required_param('id', PARAM_INT);

$PAGE->requires->js("/mod/externship/js/search.js");
require_once($CFG->libdir.'/csvlib.class.php');
require_login();

$url = new moodle_url('/mod/externship/approved-entries.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
// $PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$page = optional_param('page',0 , PARAM_INT);


$perpage = optional_param('perpage', 10, PARAM_INT);
$totalcount = $DB->count_records_sql("SELECT COUNT(DISTINCT userid) FROM {externship_data} WHERE approval = 1");
$coursemodules=$DB->get_records('course_modules',['id'=>$cmid]);
foreach($coursemodules as $coursemodule){
    $courseid=$coursemodule->course;
}


$start = $page * $perpage;
if ($start > $totalcount) {
    $page = 0;
    $start = 0;
}
 // Fetch the data from the database
 $timerecords = $DB->get_records_sql("
 SELECT od.userid,od.cmid,od.starttime,od.endtime,od.clinicname,od.preceptorname,od.description, u.firstname, u.lastname, cm.course, SUM(od.duration) AS total_duration
 FROM {externship_data} od
 JOIN {course_modules} cm ON od.cmid = cm.id
 JOIN {user} u ON u.id = od.userid
 WHERE cm.id = :cmid AND od.approval = 1
 GROUP BY od.userid, u.firstname, u.lastname, cm.course
", ['cmid' => $cmid]);


foreach($timerecords as $timerecord){
    $totalhour = ($timerecord->total_duration/60)/60;
    $totaltime =  $totalhour.' hours';
    $username =$timerecord->firstname. ' '.$timerecord->lastname;
    $userid = $timerecord->userid;
    $moduleid=$timerecord->cmid;
    $starttime = date('l jS \of F Y h:i:s A',$timerecord->starttime);
    $endtime = date('l jS \of F Y h:i:s A',$timerecord->endtime);
   
    $durationHours = floor(($timerecord->total_duration / 60)/60);
    $durationMinutes = ($timerecord->total_duration / 60) % 60;
    $duration = $durationHours .' Hours '.$durationMinutes.' Minutes';
   
    $approvedata[] = [
    'userid'=>$userid,
    'username'=> $username,
    'totaltime'=>$duration,
    'clinicname'=>$timerecord->clinicname,
    'preceptorname'=>$timerecord->preceptorname,
    'description'=>$timerecord->description,
    'starttime'=>$starttime,
    'endtime'=>$endtime,
];

}

// Ensure $approvedata is initialized as an array before using it
if (!isset($approvedata) || !is_array($approvedata)) {
    $approvedata = [];
}

// Render the template with the processed approved data
echo $OUTPUT->render_from_template(
    'mod_externship/approved-entries', 
    ['approvedata' => array_values($approvedata), 'id' => $cmid]
);

$baseurl = new moodle_url('/mod/externship/approved-entries.php');
echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
echo $OUTPUT->footer();
