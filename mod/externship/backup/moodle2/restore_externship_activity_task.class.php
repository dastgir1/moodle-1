<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The task that provides a complete restore of mod_externship is defined here.
 *
 * @package     mod_externship
 * @category    backup
 * @copyright   Copyright 2023 © PakTaleem Online Islamic School. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * externship restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */

 require_once($CFG->dirroot . '/mod/externship/backup/moodle2/restore_externship_stepslib.php'); // Because it exists (must)

 class restore_externship_activity_task extends restore_activity_task {

    /**
     * Defines particular settings for the plugin.
     */
    protected function define_my_settings() {
        // No specific settings for the externship activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        // The externship activity only has one structure step.
        $this->add_step(new restore_externship_activity_structure_step('externship_structure', 'externship.xml'));
    }

    /**
     * Define the contents in the activity that must be processed by the link decoder.
     */
    public static function define_decode_contents() {
        $contents = array();

        // Make sure 'intro' field exists in your XML.
        $contents[] = new restore_decode_content('externship', array('intro'), 'externship');
        $contents[] = new restore_decode_content('externship_data', array('file'), 'externship_data');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging to the activity to be executed by the link decoder.
     */
    public static function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('EXTERNSHIPVIEWBYID', '/mod/externship/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('EXTERNSHIPINDEX', '/mod/externship/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied by the restore_logs_processor when restoring externship logs.
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('externship', 'add', 'view.php?id={course_module}', '{externship}');
        $rules[] = new restore_log_rule('externship', 'update', 'view.php?id={course_module}', '{externship}');
        $rules[] = new restore_log_rule('externship', 'view', 'view.php?id={course_module}', '{externship}');
        $rules[] = new restore_log_rule('externship', 'report', 'report.php?id={course_module}', '{externship}');

        return $rules;
    }
  
   
}



