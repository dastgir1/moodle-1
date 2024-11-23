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
 * TODO describe file export_csv
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_login();

$url = new moodle_url('/mod/externship/export_csv.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);

// echo $OUTPUT->header();
// Check if this is an AJAX request

// Query to get approved entries
global $USER; // Ensure the global user object is accessible
$userid = $USER->id; // Get the logged-in user's ID
$cmid =optional_param('id', 0, PARAM_INT);
$approved_entries= $DB->get_records_sql("
SELECT od.userid,od.cmid, u.firstname, u.lastname, cm.course, SUM(od.duration) AS total_duration
FROM {externship_data} od
JOIN {course_modules} cm ON od.cmid = cm.id
JOIN {user} u ON u.id = od.userid
WHERE cm.id = :cmid AND od.approval = 1
GROUP BY od.userid, u.firstname, u.lastname, cm.course
", ['cmid' => $cmid]);



// Set the CSV filename
$filename = 'approved_externship_data.csv';

// Set the headers to trigger a file download in the browser
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

// Open the output stream
$output = fopen('php://output', 'w');

// Check if there are any approved entries
if ($approved_entries) {
    // Create CSV column headers (adjust the column names to match your table structure)
    $headers = array( 'User ID', 'User Name', 'Total Duration');
    fputcsv($output, $headers);

    // Loop through each entry and write it to the CSV file
    foreach ($approved_entries as $entry) {
       
        // Create a row with the data (adjust the fields to match your table structure)
        $tduration =($entry->total_duration/60)/60;
        $total_duration= $tduration.' Hours';
        $row = array(
            $entry->userid,
            $entry->firstname.' '.$entry->lastname, // Adjust if this is different in your database
            $total_duration,
             // Adjust this field based on your actual database column names
        );
        fputcsv($output, $row);
    }
} else {
    // If no approved entries, show a message
    fputcsv($output, array('No approved entries found.'));
}

// Close the output stream
fclose($output);
exit;

// echo $OUTPUT->footer();
