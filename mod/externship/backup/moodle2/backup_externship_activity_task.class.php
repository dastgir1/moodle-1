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
 * The task that provides all the steps to perform a complete backup is defined here.
 *
 * @package     mod_externship
 * @category    backup
 * @copyright   Copyright 2023 © PakTaleem Online Islamic School. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

require_once($CFG->dirroot.'/mod/externship/backup/moodle2/backup_externship_stepslib.php');

/**
 * Provides all the settings and steps to perform a complete backup of mod_externship.
 */
class backup_externship_activity_task extends backup_activity_task {

    /**
     * Defines particular settings for the plugin.
     */
    protected function define_my_settings() {
        // UI setting for including user info (userinfo).
        if ($this->get_setting_value('userinfo')) {
            $this->add_setting(new backup_activity_generic_setting('userinfo', base_setting::IS_BOOLEAN, true));
        }
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        // Add one structure step to backup externship.
        $this->add_step(new backup_externship_activity_structure_step('externship_structure', 'externship.xml'));
    }

    /**
     * Codes the transformations to perform in the activity in order to get transportable (encoded) links.
     *
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Encode link to the list of externship activities.
        $search = "/(" . $base . "\/mod\/externship\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@EXTERNSHIPINDEX*$2@$', $content);

        // Encode link to externship view by module ID.
        $search = "/(" . $base . "\/mod\/externship\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@EXTERNSHIPVIEWBYID*$2@$', $content);

        return $content;
    }
}


