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
 * Backup steps for mod_newexternship are defined here.
 *
 * @package     mod_newexternship
 * @category    backup
 * @copyright   2024 Dastgir<ghulam.dastgir@paktaleem.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_newexternship_activity_structure_step extends backup_activity_structure_step
{

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure()
    {
        // Whether to include user info or not.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separately, starting with the root element.
        $root = new backup_nested_element('newexternship', array('id'), array(
            'course',
            'name',
            'timecreated',
            'timemodified',
            'intro',
            'introformat'
        ));

        // Define the child element for the newexternship data.
        $newexternship_data = new backup_nested_element('newexternship_data', array('id'), array(
            'newexternshipid',
            'userid',
            'starttime',
            'endtime',
            'duration',
            'cmid',
            'description',
            'clinicname',
            'preceptorname',
            'approval',
            'file',
            'comments'
        ));

        // Build the tree. newexternship_data is a child of newexternship.
        $root->add_child($newexternship_data);

        // Define sources. These will be the tables used to fetch data for the elements.
        $root->set_source_table('newexternship', array('id' => backup::VAR_ACTIVITYID));

        $newexternship_data->set_source_sql(
            '
            SELECT *
              FROM {newexternship_data}
             WHERE newexternshipid = ?',
            array(backup::VAR_PARENTID)
        );

        // If we are including user info, we modify the source for newexternship_data.
        if ($userinfo) {
            $newexternship_data->set_source_table('newexternship_data', array('newexternshipid' => backup::VAR_ACTIVITYID));
        }

        // Define id annotations.
        $newexternship_data->annotate_ids('user', 'userid');

        // Define file annotations for both newexternship and newexternship_data.
        $root->annotate_files('mod_newexternship', 'intro', null);
        $newexternship_data->annotate_files('mod_newexternship', 'file', null);

        // Return the root element (newexternship) wrapped in the activity structure.
        return $this->prepare_activity_structure($root);
    }
}
