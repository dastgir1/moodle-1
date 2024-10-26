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
 * TODO describe file event
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Assuming $course, $cm, $externship, $dataid, and $externshipid are already defined
$event = \mod_externship\event\edit_data::create(array(
    'context' => context_module::instance($cm->id),
    'objectid' => $dataid,
    'courseid' => $course->id,
    'other' => array(
        'externshipid' => $externshipid,
        'externshipname' => $externship->name
    ),
    'relateduserid' => $USER->id
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('externship', $externship);
$event->trigger();