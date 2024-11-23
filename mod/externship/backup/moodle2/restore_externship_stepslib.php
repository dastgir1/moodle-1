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
 * All the steps to restore mod_externship are defined here.
 *
 * @package     mod_externship
 * @category    backup
 * @copyright   Copyright 2023 © PakTaleem Online Islamic School. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Structure step to restore one choice activity
 */
class restore_externship_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure to restore.
     */
    protected function define_structure() {
        $paths = array();

        // Define the paths for externship and externship_data
        $externship = new restore_path_element('externship', '/activity/externship');
        $externship_data = new restore_path_element('externship_data', '/activity/externship/externship_data');

        // Add paths to the structure
        $paths[] = $externship;
        $paths[] = $externship_data;

        // Return the structure
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the externship element.
     */
    protected function process_externship($data) {
        global $DB;

        // Process the externship data
        $data = (object)$data;
        $data->course = $this->get_courseid(); // Set course ID
        $oldid = $data->id; // Store old ID for mapping

        // Insert the new externship record into the database
        $newitemid = $DB->insert_record('externship', $data);

        // Apply the instance for the restore process
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process the externship_data element.
     */
    protected function process_externship_data($data) {
        global $DB;

        // Process externship_data
        $data = (object)$data;
        $oldid = $data->id;

        // Map the externshipid to the new one (parent ID)
        $data->externshipid = $this->get_new_parentid('externship');
        // $data->id = $this->get_mappingid('externship_data', $data->itemid);
        // Map the userid if necessary (handling the mapping of users)
        $data->userid = $this->get_mappingid('user', $data->userid);

        // Fetch the course module ID (cmid) from the restored externship
        $instanceid = $this->get_new_parentid('externship');

        // Use the instance ID to retrieve the corresponding course module ID (cmid)
        $cmid = $DB->get_field('course_modules', 'id', ['instance' => $instanceid, 'module' => $this->get_moduleid()]);
        if (!$cmid) {
            throw new moodle_exception('Could not retrieve cmid for the externship instance');
        }

        // Set the cmid in the data object
        $data->cmid = $cmid;

        // Insert the externship_data record into the database
        $newitemid = $DB->insert_record('externship_data', $data);
        $this->set_mapping('externship_data', $oldid, $newitemid,true);

    }

    /**
     * Helper function to get the module ID of the 'externship' activity.
     */
    protected function get_moduleid() {
        global $DB;
        static $moduleid = null;

        if (is_null($moduleid)) {
            // Fetch the module ID for the 'externship' activity type
            $moduleid = $DB->get_field('modules', 'id', ['name' => 'externship']);
        }

        return $moduleid;
    }
   
    /**
     * After executing this step, perform post-execution tasks.
     */
    protected function after_execute() {
       
        parent::after_execute();
        $this->add_related_files('mod_externship', 'intro', null);
       
        $this->add_related_files('mod_externship', 'file','externship_data');
    }

}





