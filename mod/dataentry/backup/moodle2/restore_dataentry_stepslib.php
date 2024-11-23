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
 * All the steps to restore mod_dataentry are defined here.
 *
 * @package     mod_dataentry
 * @category    backup
 * @copyright   Copyright 2023 © PakTaleem Online Islamic School. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Structure step to restore one dataentry activity
 */
class restore_dataentry_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('dataentry', '/activity/dataentry');
        // $paths[] = new restore_path_element('dataentry_data', '/activity/dataentry/dataentry_data');
        if ($userinfo) {
            $paths[] = new restore_path_element('dataentry_data', '/activity/dataentry/dataentry_data');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_dataentry($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();


        // insert the choice record
        $newitemid = $DB->insert_record('dataentry', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    // protected function process_dataentry_data($data) {
    //     global $DB;

    //     $data = (object)$data;
    //     $oldid = $data->id;

    //     $data->dataentryid = $this->get_new_parentid('dataentry');

    //     $newitemid = $DB->insert_record('dataentry_data', $data);
    //     $this->set_mapping('dataentry_data', $oldid, $newitemid);
    // }

    protected function process_dataentry_data($data) {
        global $DB;

        $data = (object)$data;

        $data->dataentryid = $this->get_new_parentid('dataentry');
      
        $data->userid = $this->get_mappingid('user', $data->userid);
        // Fetch the course module ID (cmid) from the restored dataentry
        $instanceid = $this->get_new_parentid('dataentry');

        // Use the instance ID to retrieve the corresponding course module ID (cmid)
        $cmid = $DB->get_field('course_modules', 'id', ['instance' => $instanceid, 'module' => $this->get_moduleid()]);
        if (!$cmid) {
            throw new moodle_exception('Could not retrieve cmid for the dataentry instance');
        }

        // Set the cmid in the data object
        $data->cmid = $cmid;
        $newitemid = $DB->insert_record('dataentry_data', $data);
        $this->set_mapping('dataentry_data', $oldid, $newitemid,true);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }
 /**
     * Helper function to get the module ID of the 'dataentry' activity.
     */
    protected function get_moduleid() {
        global $DB;
        static $moduleid = null;

        if (is_null($moduleid)) {
            // Fetch the module ID for the 'dataentry' activity type
            $moduleid = $DB->get_field('modules', 'id', ['name' => 'dataentry']);
        }

        return $moduleid;
    }
    protected function after_execute() {
        // Add choice related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_dataentry', 'intro', null);
        $this->add_related_files('mod_dataentry', 'file', 'dataentry_data');
    }
}





