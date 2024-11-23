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
 * @package    mod_newexternship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$event = \mod_newexternship\event\edit_data::create(array(
    'context' => context_module::instance($cm->id),
    'objectid' => $dataid,
    'courseid' => $course->id,
    'other' => array(
        'newexternshipid' => $newexternshipid,
        'newexternshipname' => $newexternship->name
    ),
    'relateduserid' => $USER->id
));
// Create the event object for the externship deletion
$event = \mod_newexternship\event\newexternship_delete::create(array(
    'objectid' => $dataid, // The ID of the record being deleted
    'context'  => \context_module::instance($cm->id), // The context of the module
    'other'    => array(
        'newexternship_name' => $newexternship->name // The name of the externship
    )
));

// Trigger the event
$event->trigger();
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('newexternship', $newexternship);
$event->trigger();