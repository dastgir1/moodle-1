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
 * All the steps to restore mod_newexternship are defined here.
 *
 * @package     mod_newexternship
 * @category    backup
 * @copyright   2024 Dastgir<ghulam.dastgir@paktaleem.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Defines the structure step to restore one mod_newexternship activity.
 */
class restore_newexternship_activity_structure_step extends restore_activity_structure_step
{

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure()
    {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        // Always restore the main newexternship activity.
        $paths[] = new restore_path_element('newexternship', '/activity/newexternship');

        // Always restore newexternship_data. 
        $paths[] = new restore_path_element('newexternship_data', '/activity/newexternship/newexternship_data');

        // You do not need to add the same path twice for userinfo; remove the duplicate path.

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processes the newexternship restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_newexternship($data)
    {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the newexternship record.
        $newitemid = $DB->insert_record('newexternship', $data);

        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Processes the newexternship_data restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_newexternship_data($data)
    {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        // Set the parent ID for the newexternship.
        $data->newexternshipid = $this->get_new_parentid('newexternship');

        // Map the user ID correctly.
        $data->userid = $this->get_mappingid('user', $data->userid);
        $instanceid = $this->get_new_parentid('newexternship');

        // Use the instance ID to retrieve the corresponding course module ID (cmid)
        $cmid = $DB->get_field('course_modules', 'id', ['instance' => $instanceid, 'module' => $this->get_moduleid()]);
        if (!$cmid) {
            throw new moodle_exception('Could not retrieve cmid for the externship instance');
        }

        // Set the cmid in the data object
        $data->cmid = $cmid;
        // Insert the newexternship_data record.
        $newitemid = $DB->insert_record('newexternship_data', $data);
        $this->set_mapping('newexternship_data', $oldid, $newitemid, true);

        // No need to save this mapping as far as nothing depend on it.
    }
    /**
     * Helper function to get the module ID of the 'externship' activity.
     */
    protected function get_moduleid()
    {
        global $DB;
        static $moduleid = null;

        if (is_null($moduleid)) {
            // Fetch the module ID for the 'externship' activity type
            $moduleid = $DB->get_field('modules', 'id', ['name' => 'newexternship']);
        }

        return $moduleid;
    }
    /**
     * Defines post-execution actions.
     */
    protected function after_execute()
    {
        // Add newexternship related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_newexternship', 'intro', null);
        $this->add_related_files('mod_newexternship', 'file', 'newexternship_data');
    }
}
