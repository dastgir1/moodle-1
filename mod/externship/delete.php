<?php

/**
 * @package    mod
 * @subpackage externship
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');



require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
// require_once(dirname(__FILE__).'/edit_form.php');

$dataid = required_param('dataid', PARAM_INT);
$confirmed = optional_param('confirmed', false, PARAM_BOOL); // confirmed deletion
$externship_data  = $DB->get_record('externship_data', array('id' => $dataid), '*', MUST_EXIST);

$externship = $DB->get_record('externship', array('id' => $externship_data->externshipid), '*', MUST_EXIST);

$course         = $DB->get_record('course', array('id' => $externship->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('externship', $externship->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$see_all = has_capability('mod/externship:manageall', $context);

/// Print the page header

$PAGE->set_url('/mod/externship/view.php', array('id' => $externship_data->id));
$PAGE->set_title(format_string($externship->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if (!$see_all && $USER->id != $externship_data->userid)
    echo notice(get_string('accessdenied', 'admin'), $CFG->wwwroot.'/mod/externship/view.php?id='.$cm->id, $course);

// if ($confirmed) 
    // add_to_log($course->id, 'offlinesession', 'delete', "delete.php?dataid=$dataid", $offlinesession->name, $cm->id);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('offlinesession-'.$somevar);



if ($confirmed) {
    if (!$DB->delete_records('externship_data', array('id'=> $dataid)))
        notice (get_string("unabletodeleteexternship", 'externship'));
    redirect($CFG->wwwroot.'/mod/externship/view.php?id='.$cm->id, get_string("externshipdataupdated", 'externship'));
}

if (!isset($msg_data) || !is_object($msg_data)) {
    $msg_data = new stdClass();
}
$msg_data->starttime = userdate($externship_data->starttime);
$msg_data->duration = format_time($externship_data->duration);
$msg_data->description = $externship_data->description;
echo $OUTPUT->confirm (
                get_string("confirmexternshipdeleted", 'externship', $msg_data),
                "delete.php?confirmed=true&dataid=$dataid",
                $CFG->wwwroot.'/mod/externship/view.php?id='.$cm->id
);
// Output starts here
echo $OUTPUT->header();
// Finish the page
echo $OUTPUT->footer();
