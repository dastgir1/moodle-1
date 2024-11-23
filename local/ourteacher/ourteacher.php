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
 * Form for editing our teacher  instances.
 * @copyright  2024  {@link http://paktaleem.com}
 * @package  local_ourteacher
 * @author    paktaleem
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__.'/lib.php');
$path = optional_param('path', '', PARAM_PATH); // $nameofarray = optional_param_array('nameofarray', null, PARAM_INT);
$pageparams = array();

if ($path) {
    $pageparams['path'] = $path;
}
$PAGE->set_url(new moodle_url('/local/ourteacher/ourteacher.php'));
$PAGE->set_context(context_system::instance());
admin_externalpage_setup('ourteacher', '', $pageparams);
$PAGE->set_pagelayout('standard');

$PAGE->requires->css('/local/ourteacher/animate/animate.min.css');
$PAGE->requires->css('/local/ourteacher/amd/css/styles.css');
$PAGE->requires->js('/local/ourteacher/wow/wow.min.js');
$PAGE->requires->js('/local/ourteacher/amd/js/main.js');

echo $OUTPUT->header();

$data=['teachers' => getTeacherRecords()];


echo $OUTPUT->render_from_template('local_ourteacher/ourteacher', $data);

echo $OUTPUT->footer();            

