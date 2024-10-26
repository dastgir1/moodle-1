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
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_dataentry_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting XML file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the dataentry instance.
        $dataentry = new backup_nested_element('dataentry', array('id'), array(
            'course', 'name', 'intro', 'introformat', 'timecreated', 'timemodified',
        ));

       
        $dataentry_data = new backup_nested_element('dataentry_data', array('id'), array(
            'dataentryid', 'userid', 'starttime','endtime', 'duration', 'description', 'approval', 'file','clinicname','preceptorname','comments'
        ));
        // If there are images (assuming stored in mdl_files), use files element
        // $files = new backup_nested_element('files');

        // Build the tree by adding dataentry_data_rec as a child of dataentry_data and dataentry_data as a child of dataentry.
        $dataentry->add_child($dataentry_data);
        // $dataentry_data->add_child($dataentry_data_rec);

        // Define the source table for the dataentry instance.
        $dataentry->set_source_table('dataentry', array('id' => backup::VAR_ACTIVITYID));

        // If user information is included, get all relevant entries from dataentry_data.
        if ($userinfo) {
            $dataentry_data->set_source_sql('
                SELECT id, dataentryid, userid, starttime,endtime, duration, description, approval, file,clinicname,preceptorname,comments
                  FROM {dataentry_data}
                 WHERE dataentryid = ?',
                array(backup::VAR_PARENTID));
        } 
        else {
            // If no user information, backup the dataentry data without user details.
            $dataentry_data->set_source_sql('
                SELECT id, dataentryid, starttime,endtime, duration, description, approval, file,clinicname,preceptorname,comments
                  FROM {dataentry_data}
                 WHERE dataentryid = ?',
                array(backup::VAR_PARENTID));
        }
     // Include file areas (assuming dataentry data has image files)
        //  $files->set_source_table('files', array('itemid' => backup::VAR_PARENTID, 'filearea' => 'file'));
        // Define ID annotations to handle user data in the dataentry_data.
        $dataentry_data->annotate_ids('user', 'userid');

        // Define file annotations for the dataentry intro area.
        $dataentry->annotate_files('mod_dataentry', 'intro', null);
        
        // Define file annotations for user-uploaded files in dataentry_data.
        $dataentry_data->annotate_files('mod_dataentry', 'file', null);

        // Return the prepared activity structure for backup.
        return $this->prepare_activity_structure($dataentry);
    }
}



