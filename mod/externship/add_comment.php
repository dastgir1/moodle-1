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
 * TODO describe file add_comment
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
// $PAGE->requires->js("/mod/externship/js/jquery.min.js");
// $PAGE->requires->js("/mod/externship/js/script.js");

require_login();

$url = new moodle_url('/mod/externship/add_comment.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
// $PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
// $comment = required_param('comment',PARAM_TEXT);
$id = required_param('id',PARAM_INT);
$cmid = required_param('cmid',PARAM_INT);

if(isset($_POST['submit'])){
    $comment = $_POST['comment'];
    $newdata = new stdClass();
    $newdata->id =$id;
    $newdata->comments =$comment;
    $updaterecord =$DB->update_record('externship_data',$newdata);
    if($updaterecord){
        notice(get_string("externshipdataupdated", 'externship'),$CFG->wwwroot.'/mod/externship/view.php?id='.$cmid);
    }
   
}

echo $OUTPUT->footer();