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
 * TODO describe file dataentryform
 *
 * @package    mod_dataentry
 * @copyright  2024 dastgirmoodledeveloper@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$PAGE->requires->js("/mod/dataentry/js/index.js");
require_once(dirname(__FILE__) . '/lib.php');
$dataid = optional_param('dataid', 0, PARAM_INT);

$dataentryid = optional_param('dataentryid', 0, PARAM_INT);


if ($dataid) {
    $dataentry_data  = $DB->get_record('dataentry_data', array('id' => $dataid), '*', MUST_EXIST);
    $dataentryid = $dataentry_data->dataentryid;
} elseif (!$dataentryid) {
    // error('You must specify a data ID or an offline session ID');
}
$dataentry = $DB->get_record('dataentry', array('id' => $dataentryid), '*', MUST_EXIST);

$course         = $DB->get_record('course', array('id' => $dataentry->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('dataentry', $dataentry->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_login();

$url = new moodle_url('/mod/dataentry/dataentryform.php', ['courseid' => $course->id]);
$PAGE->set_url($url);
$PAGE->set_context(context_course::instance($course->id));
$PAGE->set_title('Data Entry Form');
$PAGE->set_heading($dataentry->name);
$PAGE->navbar->add($dataentry->name, new moodle_url('/mod/dataentry/view.php', array('id' => $cm->id)));
$PAGE->navbar->add('Data Entry Form');
echo $OUTPUT->header();

// Instantiate the myform form from within the plugin.
$mform = new \mod_dataentry\form\dataentryform(null, ['dataid' => $dataid, 'dataentryid' => $dataentryid]);

// Form processing and displaying is dataentry
if ($mform->is_cancelled()) {
    // If there is a cancel element on the form, and it was pressed,
    // then the `is_cancelled()` function will return true.
    // You can handle the cancel operation here.
} else if ($data = $mform->get_data()) {

    $aray = explode(':', $data->duration);
    $hs = $aray[0] * 3600;
    $ms = $aray[1] * 60;
    $durationsecods = $hs + $ms;

    $datarecord = new stdClass();
    
    $datarecord->dataentryid = $data->dataentryid;
    $datarecord->userid = $USER->id;
    $datarecord->starttime = mktime($data->starthour, $data->startminute, 0, $data->month, $data->day, $data->year);
    $datarecord->endtime = mktime($data->endhour, $data->endminute, 0, $data->month, $data->day, $data->year);
    $datarecord->duration = $durationsecods;
    $datarecord->cmid = $cm->id;
    $datarecord->description = $data->description;
    $datarecord->file = $data->file;
    $datarecord->clinicname = $data->clinicname;
    $datarecord->preceptorname = $data->preceptorname;

    if (!$dataid) {

        $datarecord->id = $DB->insert_record('dataentry_data', $datarecord);
        $maxbytes = get_max_upload_sizes();
        if ($datarecord->id) {
            file_save_draft_area_files(
                $data->file,
                $context->id,
                'mod_dataentry',
                'file',
                $datarecord->id,
                [
                    'subdirs' => 0,
                    'maxbytes' => $maxbytes,
                    'maxfiles' => 1,
                ]
            );
        }
        // notice(get_string("dataentryadded", 'dataentry'), '/mod/dataentry/view.php?id=' . $cm->id . '');
    } else {
        $datarecord->id = $dataid;
        $update = $DB->update_record('dataentry_data', $datarecord);
        if ($update) {

            // Fetch the entry being edited, or create a placeholder.
            $maxbytes = get_max_upload_sizes();
            file_save_draft_area_files(
                $data->file,
                $context->id,
                'mod_dataentry',
                'file',
                $datarecord->id,
                [
                    'subdirs' => 0,
                    'maxbytes' => $maxbytes,
                    'maxfiles' => 1,
                ]
            );
        }
    }
    notice(get_string("dataentryupdated", 'dataentry'), '/mod/dataentry/view.php?id=' . $cm->id . '');
} else {
    if (empty($dataid)) {
        $entry = (object) [
            'id' => null,
        ];
    } else {
        $entry = $DB->get_record('dataentry_data', ['id' => $dataid]);
   

    // Get an unused draft itemid which will be used for this form.
    $draftitemid = file_get_submitted_draft_itemid('file');

    // Copy the existing files which were previously uploaded
    // into the draft area used by this form.
    file_prepare_draft_area(
        // The $draftitemid is the target location.
        $draftitemid,

        $context->id,
        'mod_dataentry',
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
    $totaldurationminutes = ($entry->duration) / 60;
    $durationhours = intval(floor(($totaldurationminutes) / 60));
    $durationminutes = $totaldurationminutes % 60;
    $entry->duration = $durationhours . ':' . $durationminutes;
    $mform->set_data($entry);
}
    // Display the form.
    $mform->display();
}
echo $OUTPUT->footer();
