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

require_once(dirname(__FILE__) . '/includes/layoutdata.php');
global $DB;

$PAGE->requires->css(new moodle_url('/theme/academi/style/academi.css'));
$PAGE->requires->css(new moodle_url('/theme/academi/style/owl.css'));
$PAGE->requires->css(new moodle_url('/theme/academi/style/animate.css'));
// $PAGE->requires->js_call_amd('theme_academi/frontpage', 'init');
$PAGE->requires->js('/theme/academi/amd/build/easing.min.js');
$PAGE->requires->js('/theme/academi/amd/build/wow.min.js');
$PAGE->requires->js('/theme/academi/amd/build/waypoints.min.js');
$PAGE->requires->js('/theme/academi/amd/src/easing.js');
$PAGE->requires->js('/theme/academi/amd/src/wow.js');
$PAGE->requires->js('/theme/academi/js/owl.carousel.js');
$PAGE->requires->js('/theme/academi/js/custom.js');

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
// Jumbotron class.
$jumbotronclass = (!empty(theme_academi_get_setting('jumbotronstatus'))) ? 'jumbotron-element' : '';
$video_url = theme_academi_get_video_url('homevideomedia');
// $video_url = get_config('theme_academi', 'homevideomedia');

$renderer = $PAGE->get_renderer('core', 'course');
$courseslist = $renderer->available_courselist();
$categoryslist = $renderer->coursecategorylist();
$teacherlist = teacherlist();

$templatecontext += [
    'bodyattributes' => $bodyattributes,
    'jumbotronclass' => $jumbotronclass,
    'vediolink' => $video_url,
    'categoryslist' => $categoryslist,
    'courses' => $courseslist,
    'teacherlist' => $teacherlist,
    'comments' => get_comments(),
];

echo $OUTPUT->render_from_template('theme_academi/newfrontpage', $templatecontext);
