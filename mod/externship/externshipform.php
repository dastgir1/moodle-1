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
 * TODO describe file externshipform
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__).'/lib.php');
// require_once(dirname(__FILE__).'/locallib.php');
// require_once($CFG->dirroot.'/mod/externship/form/externshipform.php');
$PAGE->requires->js("/mod/externship/js/jquery.min.js");
$PAGE->requires->js("/mod/externship/js/script.js");
$dataid = optional_param('dataid',0,PARAM_INT);

$externshipid = optional_param('externshipid',0,PARAM_INT);

if ($dataid) {
    $externship_data  = $DB->get_record('externship_data', array('id' => $dataid), '*', MUST_EXIST);
    $externshipid = $externship_data->externshipid;
} elseif (!$externshipid) {
    // error('You must specify a data ID or an offline session ID');
}
$externship = $DB->get_record('externship', array('id' =>$externshipid), '*', MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $externship->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('externship', $externship->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_login();

$url = new moodle_url('/mod/externship/externshipform.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Externship Form');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($externship->name);
$PAGE->navbar->add($externship->name, new moodle_url('/mod/externship/view.php', array('id' => $cm->id)));
$PAGE->navbar->add('Externship Form');

// Instantiate the myform form from within the plugin.
$mform = new \mod_externship\form\externshipform(null,['dataid'=>$dataid,'externshipid'=>$externshipid]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
//  redirect('/mod/externship/view.php');
} else if ($data = $mform->get_data()) {
    $durationarray=explode(' ',$data->duration);

    $durationsecods = ($durationarray[0]*3600)+($durationarray[2]*60);

   $externship_record = new stdClass();
   if ($dataid) $externship_record->id=$dataid;
   $externship_record->externshipid = $externship->id;
   $externship_record->userid = $USER->id;
   $externship_record->starttime=mktime ($data->starthour, $data->startminute, 0, $data->month, $data->day, $data->year);
   $externship_record->endtime=mktime ($data->endhour, $data->endminute, 0, $data->month, $data->day, $data->year);
//    $externship_record->duration = $data->durationhour * 3600 + $data->durationminute *60;
   $externship_record->cmid = $cm->id;
   $externship_record->duration = $durationsecods;
   $externship_record->description = $data->description;
   $externship_record->file = $data->file;
   $externship_record->clinicname = $data->clinicname;
   $externship_record->preceptorname = $data->preceptorname;
   if (!$dataid) {
       $externship_record->id = $DB->insert_record('externship_data', $externship_record);
       if (!$externship_record->id) notice(get_string("unabletoaddexzternshipdata", 'externship'));
        $maxbytes = get_max_upload_sizes();
        if($externship_record->id){
            file_save_draft_area_files(
                $data->file,
                $context->id,
                'mod_externship',
                'file',
                $externship_record->id,
                [
                    'subdirs' => 0,
                    'maxbytes' =>$maxbytes,
                    'maxfiles' => 1,
                ]
            );
        }
   }elseif (!$DB->update_record ('externship_data', $externship_record))
   notice(get_string("unabletoupdateexternshipdata", 'externship'));
   // Fetch the entry being edited, or create a placeholder.
   $maxbytes = get_max_upload_sizes();
   file_save_draft_area_files(
       $data->file,
       $context->id,
       'mod_externship',
       'file',
       $externship_record->id,
       [
           'subdirs' => 0,
           'maxbytes' =>$maxbytes,
           'maxfiles' => 1,
       ]
   );
  

   redirect($CFG->wwwroot.'/mod/externship/view.php?id='.$cm->id, get_string("externshipdataupdated", 'externship'));

        
   
} else {
    if (empty($dataid)) {
        $entry = (object) [
            'id' => null,
        ];
    } else {
        $entry = $DB->get_record('externship_data', ['id' => $dataid]);
   
        // Get an unused draft itemid which will be used for this form.
        $draftitemid = file_get_submitted_draft_itemid('file');

        file_prepare_draft_area(
            // The $draftitemid is the target location.
            $draftitemid,

            $context->id,
            'mod_externship',
            'file',
            $entry->id,
            [
                'subdirs' => 0,
                'maxbytes' => $CFG->maxbytes,
                'maxfiles' => 1,
            ]
        );

        // Set the itemid of draft area that the files have been moved to.
        $entry->id = $dataid;
        $entry->file = $draftitemid;
        $durationhour     = intval(intval($entry->duration) / 3600);
        $durationminute   = intval((intval($entry->duration) - $durationhour * 3600) /60);
        $entry->duration = $durationhour.' Hours '.$durationminute.' Minutes';
        $mform->set_data($entry);
    }
}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
