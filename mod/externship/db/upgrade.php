<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade steps for mod_externship
 *
 * Documentation: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @package    mod_externship
 * @category   upgrade
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_externship_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2024100401) {

        // Define table externship to be created.
        $table = new xmldb_table('externship_data');

        // Adding fields to table externship.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('externshipid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('starttime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('duration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_CHAR, '500', null, XMLDB_NOTNULL, null, null);
        $table->add_field('approval', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('file', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
      

        // Adding keys to table externship.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_course', XMLDB_KEY_FOREIGN, ['externshipid'], 'externshipid', ['id']);

        // Conditionally launch create table for externship.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Externship savepoint reached.
        upgrade_mod_savepoint(true, 2024100401, 'externship');
    }
    if ($oldversion < 2024101303) {

        // Define the table we want to update.
        $table = new xmldb_table('externship_data');

        // Define the new field/column with its specifications.
        // $field = new xmldb_field('approval', XMLDB_TYPE_TEXT, null, null, null, 0, null, 'description');
        $field = new xmldb_field('endtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'starttime');
       
        // Check if the field already exists to avoid errors.
        if (!$dbman->field_exists($table, $field)) {
            // Add the new field to the table.
            $dbman->add_field($table, $field);
        }

        // Update the plugin version to the new version.
        upgrade_mod_savepoint(true, 2024101303, 'externship');
    }
    if ($oldversion < 2024101304) {

        // Define the table we want to update.
        $table = new xmldb_table('externship_data');

        // Define the new field/column with its specifications.
        // $field = new xmldb_field('approval', XMLDB_TYPE_TEXT, null, null, null, 0, null, 'description');
        $field = new xmldb_field('clinicname', XMLDB_TYPE_CHAR, '500', null, XMLDB_NOTNULL, null, null,'file');
       
        // Check if the field already exists to avoid errors.
        if (!$dbman->field_exists($table, $field)) {
            // Add the new field to the table.
            $dbman->add_field($table, $field);
        }

        // Update the plugin version to the new version.
        upgrade_mod_savepoint(true, 2024101304, 'externship');
    }
    if ($oldversion < 2024101305) {

        // Define the table we want to update.
        $table = new xmldb_table('externship_data');

        // Define the new field/column with its specifications.
        // $field = new xmldb_field('approval', XMLDB_TYPE_TEXT, null, null, null, 0, null, 'description');
        $field = new xmldb_field('preceptorname', XMLDB_TYPE_CHAR, '500', null, XMLDB_NOTNULL, null, null,'clinicname');
       
        // Check if the field already exists to avoid errors.
        if (!$dbman->field_exists($table, $field)) {
            // Add the new field to the table.
            $dbman->add_field($table, $field);
        }

        // Update the plugin version to the new version.
        upgrade_mod_savepoint(true, 2024101305, 'externship');
    }
    if ($oldversion < 2024101306) {

        // Define the table we want to update.
        $table = new xmldb_table('externship_data');

        // Define the new field/column with its specifications.
        // $field = new xmldb_field('approval', XMLDB_TYPE_TEXT, null, null, null, 0, null, 'description');
        $field = new xmldb_field('comments', XMLDB_TYPE_CHAR, '500', null, XMLDB_NOTNULL, null, null,'preceptorname');
       
        // Check if the field already exists to avoid errors.
        if (!$dbman->field_exists($table, $field)) {
            // Add the new field to the table.
            $dbman->add_field($table, $field);
        }

        // Update the plugin version to the new version.
        upgrade_mod_savepoint(true, 2024101306, 'externship');
    }
    return true;
}
