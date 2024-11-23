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
 * Backup steps for mod_dataentry are defined here.
 *
 * @package     mod_dataentry
 * @category    backup
 * @copyright   Copyright 2023 © PakTaleem Online Islamic School. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.


/**
 * Define the complete dataentry structure for backup, with file and id annotations
 */     
class backup_dataentry_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $dataentry = new backup_nested_element('dataentry', array('id'), array(
          'course',  'name', 'intro', 'introformat','timecreated', 'timemodified'));


        $dataentry_data = new backup_nested_element('dataentry_data');

        $dataentry_data = new backup_nested_element('dataentry_data', array('id'), array(
            'dataentryid','userid', 'starttime','endtime', 'duration', 'description', 'approval', 'file','clinicname','preceptorname','comments'));

        // Build the tree
        $dataentry->add_child($dataentry_data);
        // Define sources
        $dataentry->set_source_table('dataentry', array('id' => backup::VAR_ACTIVITYID));

        $dataentry_data->set_source_sql('SELECT * FROM {dataentry_data} WHERE dataentryid = ?', array(backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $dataentry_data->set_source_table('dataentry_data', array('dataentryid'=>backup::VAR_PARENTID));
        }
        // Define id annotations
        $dataentry_data->annotate_ids('user', 'userid');
        // Define file annotations
        $dataentry->annotate_files('mod_dataentry', 'intro', null);
        $dataentry_data->annotate_files('mod_dataentry', 'file',null);
        // Return the root element (dataentry), wrapped into standard activity structure
        return $this->prepare_activity_structure($dataentry);
    }
}



