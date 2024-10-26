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
 * TODO describe file status
 *
 * @package    mod_dataentry
 * @copyright  2024 dastgirmoodledeveloper@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();
$dataid = required_param('dataid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$url = new moodle_url('/mod/dataentry/status.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$newrecord = new stdClass();
$newrecord->id = $dataid;

$newrecord->approval = 1;


$updaterecord = $DB->update_record('dataentry_data', $newrecord);

if ($updaterecord) {
    redirect($CFG->wwwroot . '/mod/dataentry/view.php?id=' . $cmid, get_string("dataentryupdated", 'dataentry'));
}
echo $OUTPUT->footer();
