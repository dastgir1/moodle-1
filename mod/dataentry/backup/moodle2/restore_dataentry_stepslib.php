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
 * @copyright   2024 Syed Dastgir <ghulam.dastgir@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Defines the structure step to restore one mod_dataentry activity.
 */
class restore_dataentry_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('dataentry', '/activity/dataentry');
        $paths[] = new restore_path_element('dataentry_data', '/activity/dataentry/dataentry_data');
        

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }
    protected function process_dataentry($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the dataentry record
        $newitemid = $DB->insert_record('dataentry', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
    
    protected function process_dataentry_data($data) {
        global $DB;
    
        $data = (object)$data;
    
        // Get new parent ID for dataentry
        $data->dataentryid = $this->get_new_parentid('dataentry');
        
        // Map dataentry_data option ID
        $data->dataid = $this->get_mappingid('dataentry_data', $data->dataentryid);
        
        // Map user ID
        $data->userid = $this->get_mappingid('user', $data->userid);
        
        // Get the course module ID based on the dataentry instance
        $cmid = $DB->get_field('course_modules', 'id', ['instance' => $data->dataentryid, 'module' =>$this->get_moduleid()]);
        
        // Add the course module ID to the dataentry_data object
        $data->cmid = $cmid;
        
        // Insert record into dataentry_data table
        $newitemid = $DB->insert_record('dataentry_data', $data);
        // $this->add_related_files('mod_dataentry', 'file', 'dataentry_data', $newitemid);
        // $this->set_mapping('dataentry_data', $data->dataid, $newitemid, true);
        // No need to save this mapping as far as nothing depend on it (child paths, file areas nor links decoder)
    }
    
    /**
     * Fetches the module ID for the 'dataentry' activity type.
     *
     * @return int|null The module ID for 'dataentry', or null if not found.
     */
    function get_moduleid() {
        global $DB;
        static $moduleid = null;
    
        // Check if the module ID has already been fetched
        if (is_null($moduleid)) {
            // Retrieve the module ID for the 'dataentry' activity from the 'modules' table
            $moduleid = $DB->get_field('modules', 'id', ['name' => 'dataentry']);
            
            // If module ID is not found, throw an exception
            if (!$moduleid) {
                throw new moodle_exception('Could not retrieve module ID for dataentry');
            }
        }
    
        return $moduleid;
    }
    
  
    /**
     * Defines post-execution actions.
     */
    protected function after_execute() {
        // Add dataentry related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_dataentry', 'intro', null);
        $this->add_related_files('mod_dataentry', 'file', null);
    }
}
