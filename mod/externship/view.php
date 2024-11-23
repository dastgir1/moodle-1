<?php

/**
 * @package    mod
 * @subpackage externship
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
';
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/accesslib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // offlinesession instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('externship', $id, 0, false, MUST_EXIST);

    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $externship  = $DB->get_record('externship', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $externship  = $DB->get_record('externship', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $externship->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('externship', $externship->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$see_all = has_capability('mod/externship:manageall', $context);
$modinfo = get_fast_modinfo($course);

$PAGE->set_url('/mod/externship/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($externship->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

global $PAGE;

if (has_capability('mod/externship:canapproveentries', $context)) {
    $cmid =optional_param('id', 0, PARAM_INT); // Get the course module ID.
$approved = get_string('approved','externship');
    echo "
    <ul class='nav nav-tabs'>
        <li class='nav-item border rounded'>
            <a class='nav-link' href='/mod/externship/approved-entries.php?id=$cmid&name=$externship->name'>$approved</a>
        </li>
     
    </ul>
    ";
}

 // Assuming you pass course module ID in URL

// Get the module context
$context = context_module::instance($id);

$student_role_id = 5; // Moodle's default student role ID

if (user_has_role_assignment($USER->id, $student_role_id, $context->id)) {
    // Fetch the data from the database
    global $USER; // Ensure the global user object is accessible
    $userid = $USER->id; // Get the logged-in user's ID
    
    $timerecords = $DB->get_records_sql("
        SELECT cm.course, SUM(od.duration) AS total_duration
        FROM {externship_data} od
        JOIN {course_modules} cm ON od.cmid = cm.id
        WHERE cm.id = :cmid AND od.approval = 1 AND od.userid = :userid
        GROUP BY cm.course
    ", [
        'cmid' => $id,
        'userid' => $userid // Bind the logged-in user's ID
    ]);

    // Display the data in an HTML table
    if ($timerecords) {
        foreach ($timerecords as $entry) {
            $duration = ($entry->total_duration/60)/60;
            $total_duration=$duration . ' Hours'; 
            echo 'Externship Total Hours: ' . $total_duration ;           
        }

    } else {
        echo '<p>No entries approved.</p>';
    }
} else {

}

if ($externship->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('externship', $externship, $cm->id), 'generalbox mod_introbox', 'externshipintro');
}

$content = externship_get_list($externship, $see_all);

echo $OUTPUT->box($content, 'generalbox');





// Finish the page
echo $OUTPUT->footer();
