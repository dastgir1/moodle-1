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
 * TODO describe file newexport_csv
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/mod/externship/newexport_csv.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
$cmid = required_param('id', PARAM_INT);

$entries = $DB->get_records('externship_data', array('cmid' => $cmid), 'starttime DESC');
// Set the CSV filename
$filename = 'externship_data.csv';

// Set the headers to trigger a file download in the browser
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Open the output stream
$output = fopen('php://output', 'w');

// Check if there are any approved entries
if ($entries) {
    // Create CSV column headers (adjust the column names to match your table structure)
    $headers = array('User Name', 'Date', 'Start Time', 'End Time', 'Duration', 'Clinic Name', 'Preceptor Name', 'status', 'Description');
    fputcsv($output, $headers);

    // Loop through each entry and write it to the CSV file
    foreach ($entries as $entry) {
        $user = $DB->get_record('user', ['id' => $entry->userid]);
        $date = date('D d M Y', $entry->date);
        $starttime = date('H:i A', $entry->starttime);
        $endtime = date('H:i A', $entry->endtime);
        $durationhour     = intval(intval($entry->duration) / 3600);
        $durationminute   = intval((intval($entry->duration) - $durationhour * 3600) / 60);
        $duration = $durationhour . ' Hours ' . $durationminute . ' Minutes';
        if ($entry->approval == 0) {
            $status = 'Not Approve';
        } else {
            $status = 'Approved';
        }
        $row = array(
            $user->firstname . ' ' . $user->lastname, // Adjust if this is different in your database
            $date,
            $starttime,
            $endtime,
            $duration,
            $entry->clinicname,
            $entry->preceptorname,
            $status,
            $entry->description,
            // Adjust this field based on your actual database column names
        );
        fputcsv($output, $row);
    }
} else {
    // If no approved entries, show a message
    fputcsv($output, array('No  entries found.'));
}

// Close the output stream
fclose($output);
exit;

