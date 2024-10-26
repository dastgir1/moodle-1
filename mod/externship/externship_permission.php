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
 * TODO describe file externship_permission
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/mod/externship/externship_permission.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pageLayout('standard');
$PAGE->set_heading($SITE->fullname);



$externshipid = optional_param('dataid',0,PARAM_INT);
$cmid = $DB->get_record_sql("SELECT cmid FROM {externship_data} WHERE id =$externshipid");

$record = new stdClass();
$record->id = $externshipid;
$record->approval = 1;
$updaterecord = $DB->update_record('externship_data',$record);

if($updaterecord){
    redirect($CFG->wwwroot.'/mod/externship/view.php?id='.$cmid->cmid, get_string("externshipdataupdated", 'externship'));
}
echo $OUTPUT->header();
echo $OUTPUT->footer();
