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
 * Backup steps for mod_externship are defined here.
 *
 * @package     mod_externship
 * @category    backup
 * @copyright   Copyright 2023 © PakTaleem Online Islamic School. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_externship_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting XML file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the externship instance.
        $externship = new backup_nested_element('externship', array('id'), array(
            'course', 'name', 'intro', 'introformat', 'timecreated', 'timemodified',
        ));


        $externship_data = new backup_nested_element('externship_data', array('id'), array(
            'externshipid', 'userid', 'starttime','endtime', 'duration', 'description', 'approval', 'file','clinicname','preceptorname','comments'
        ));

        // Build the tree by adding externship_data_rec as a child of externship_data and externship_data as a child of externship.
        $externship->add_child($externship_data);
        // $externship_data->add_child($externship_data_rec);

        // Define the source table for the externship instance.
        $externship->set_source_table('externship', array('id' => backup::VAR_ACTIVITYID));

        // If user information is included, get all relevant entries from externship_data.
        $externship_data->set_source_table('externship_data', array('externshipid' => backup::VAR_PARENTID));


        // Define ID annotations to handle user data in the externship_data.
        $externship_data->annotate_ids('user', 'userid');

        // Define file annotations for the externship intro area.
        $externship->annotate_files('mod_externship', 'intro', null);

        // Define file annotations for user-uploaded files in externship_data.
        $externship_data->annotate_files('mod_externship', 'file', null);

        // Return the prepared activity structure for backup.
        return $this->prepare_activity_structure($externship);
    }
}



