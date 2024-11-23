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
 * TODO describe file newexternshipform
 *
 * @package    mod_newexternship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
// $PAGE->requires->js("/mod/newexternship/js/jquery.min.js");
$PAGE->requires->js("/mod/newexternship/js/script.js");
require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__).'/lib.php');
// require_once(dirname(__FILE__).'/locallib.php');
// require_once($CFG->dirroot.'/mod/externship/form/externshipform.php');
$dataid = optional_param('dataid',0,PARAM_INT);

$newexternshipid = optional_param('newexternshipid',0,PARAM_INT);

if ($dataid) {
    $newexternship_data  = $DB->get_record('newexternship_data', array('id' => $dataid), '*', MUST_EXIST);
    $newexternshipid = $newexternship_data->newexternshipid;
} elseif (!$newexternshipid) {
    // error('You must specify a data ID or an offline session ID');
}
$newexternship = $DB->get_record('newexternship', array('id' =>$newexternshipid), '*', MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $newexternship->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('newexternship', $newexternship->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_login();

$url = new moodle_url('/mod/newexternship/newexternshipform.php', ['courseid' => $course->id]);
$PAGE->set_url($url);
$PAGE->set_context(context_course::instance($course->id));
$PAGE->set_title('New Externship Form');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($newexternship->name);
$PAGE->navbar->add($newexternship->name, new moodle_url('/mod/newexternship/view.php', array('id' => $cm->id)));
$PAGE->navbar->add('New Externship Form');
// Instantiate the myform form from within the plugin.
$mform = new \mod_newexternship\form\newexternshipform(null,['dataid'=>$dataid,'newexternshipid'=>$newexternshipid]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
//  redirect('/mod/externship/view.php');
} else if ($data = $mform->get_data()) {
    print_object($data);
    $durationarray= str_split($data->duration,2);
    $durationsecods = ($durationarray[0]*60)*60+($durationarray[4]*60);

   $newexternship_record = new stdClass();
   if ($dataid) $newexternship_record->id=$dataid;
   $newexternship_record->newexternshipid = $newexternship->id;
   $newexternship_record->userid = $USER->id;
   $newexternship_record->starttime=mktime ($data->starthour, $data->startminute, 0, $data->month, $data->day, $data->year);
   $newexternship_record->endtime=mktime ($data->endhour, $data->endminute, 0, $data->month, $data->day, $data->year);
   $newexternship_record->duration = $durationsecods;
   $newexternship_record->cmid = $cm->id;
   $newexternship_record->description = $data->description;
   $newexternship_record->clinicname = $data->clinicname;
   $newexternship_record->preceptorname = $data->preceptorname;
   $newexternship_record->file = $data->file;
   if (!$dataid) {
       $newexternship_record->id = $DB->insert_record('newexternship_data', $newexternship_record);
       if (!$newexternship_record->id) notice(get_string("unabletoaddexzternshipdata", 'newexternship'));
        $maxbytes = get_max_upload_sizes();
        if($newexternship_record->id){
            file_save_draft_area_files(
                $data->file,
                $context->id,
                'mod_externship',
                'file',
                $newexternship_record->id,
                [
                    'subdirs' => 0,
                    'maxbytes' =>$maxbytes,
                    'maxfiles' => 1,
                ]
            );
        }
   }elseif (!$DB->update_record ('newexternship_data', $newexternship_record))
   notice(get_string("unabletoupdateexternshipdata", 'newexternship'));
   // Fetch the entry being edited, or create a placeholder.
   $maxbytes = get_max_upload_sizes();
   file_save_draft_area_files(
       $data->file,
       $context->id,
       'mod_newexternship',
       'file',
       $newexternship_record->id,
       [
           'subdirs' => 0,
           'maxbytes' =>$maxbytes,
           'maxfiles' => 1,
       ]
   );
  

   redirect($CFG->wwwroot.'/mod/newexternship/view.php?id='.$cm->id, get_string("newexternshipdataupdated", 'newexternship'));

        
   
} else {
    if (empty($dataid)) {
        $entry = (object) [
            'id' => null,
        ];
    } else {
        $entry = $DB->get_record('newexternship_data', ['id' => $dataid]);
       
  

    // Get an unused draft itemid which will be used for this form.
    $draftitemid = file_get_submitted_draft_itemid('file');

    // Copy the existing files which were previously uploaded
    // into the draft area used by this form.
    file_prepare_draft_area(
        // The $draftitemid is the target location.
        $draftitemid,

        $context->id,
        'mod_newexternship',
        'file',
        $entry->id,
        [
            'subdirs' => 0,
            'maxbytes' => $CFG->maxbytes,
            'maxfiles' => 1,
        ]
    );

    // Set the itemid of draft area that the files have been moved to.
    $durationhours =intval(floor($entry->duration/3600));
    $durationminute   = intval((intval($entry->duration) - $durationhours * 3600) /60);
    $entry->id = $dataid;
    $entry->file = $draftitemid;
    $entry->duration = $durationhours.' Hours '.$durationminute.' Minutes';
    

    $mform->set_data($entry);
}
}
echo $OUTPUT->header();

$mform->display();
echo $OUTPUT->footer();
