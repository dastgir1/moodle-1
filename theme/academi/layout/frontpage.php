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
 * A drawer based layout for the boost theme.
 *
 * @package   theme_academi
 * @copyright 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $DB;
require_once(dirname(__FILE__) . '/includes/layoutdata.php');
require_once(dirname(__FILE__) . '/includes/homeslider.php');
$PAGE->requires->js('/theme/academi/js/custom.js');
$PAGE->requires->css(new moodle_url('/theme/academi/style/slick.css'));
$PAGE->requires->js_call_amd('theme_academi/frontpage', 'init');
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
// Jumbotron class.
$jumbotronclass = (!empty(theme_academi_get_setting('jumbotronstatus'))) ? 'jumbotron-element' : '';
$homevediomedia = (!empty(theme_academi_get_setting('homevideomedia'))) ? 'homevideomedia-element' : '';
$description = get_string('mbadescription', 'theme_academi');
$onlineclass = get_string('onlineclass', 'theme_academi');
$realmeeting = get_string('realmeeting', 'theme_academi');
// Slide show contnet added in the templatecontext.
$courses = $DB->get_records_sql("SELECT * FROM {course} WHERE id != ?", [1]);
foreach ($courses as $course) {

    $coursename = $course->fullname;
    $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
}
$categories = $DB->get_records('course_categories');

foreach ($categories as $category) {


    $name = $category->name;
    $description2 = $category->description;
    $coursecount = $category->coursecount;
}
// Slide show contnet added in the templatecontext.
$templatecontext += $sliderconfig;
$templatecontext += [
    'bodyattributes' => $bodyattributes,
    'jumbotronclass' => $jumbotronclass,
    'vediolink' => $homevediomedia,
    'description' => $description,
    'onlineclass' => $onlineclass,
    'realmeeting' => $realmeeting,
    'coursename' => $coursename,
    'courselink' => $courseurl,
    'name' => $name,
    'description2' => $description2,
    'coursecount' => $coursecount,

];

echo $OUTPUT->render_from_template('theme_academi/frontpage', $templatecontext);
