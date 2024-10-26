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
 * TODO describe file deleterecord
 *
 * @package    mod_dataentry
 * @copyright  2024 dastgirmoodledeveloper@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$dataid = required_param('dataid', PARAM_INT);



require_login();

$url = new moodle_url('/mod/dataentry/deleterecord.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

$deleterecord = $DB->delete_records('dataentry_data', ['id' => $dataid]);
if ($deleterecord) {
    echo 1;
} else {
    echo 0;
}
echo $OUTPUT->footer();
