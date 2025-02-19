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
 * TODO describe file category
 *
 * @package    local_ourteacher
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/ourteacher/category.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);

$PAGE->requires->css('/local/ourteacher/lib/animate/animate.min.css');
$PAGE->requires->css('/local/ourteacher/lib/animate/animate.css');
$PAGE->requires->css('/local/ourteacher/amd/css/style.css');
$PAGE->requires->js('/local/ourteacher/wow/wow.min.js');
$PAGE->requires->js('/local/ourteacher/lib/easing/easing.min.js');
$PAGE->requires->js('/local/ourteacher/lib/waypoints/waypoints.min.js');
$PAGE->requires->js('/local/ourteacher/amd/js/main.js');
echo $OUTPUT->header();
$categories = $DB->get_records('course_categories');

$catdata = [];
foreach ($categories as $category) {

    $catdata[] = [
        'name' => $category->name,
        'description' => $category->description,
        'coursecount' => $category->coursecount,
    ];
}

echo $OUTPUT->render_from_template('local_ourteacher/category', ['catdata' => $catdata]);
echo $OUTPUT->footer();
