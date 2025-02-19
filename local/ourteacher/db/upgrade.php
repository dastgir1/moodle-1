<?php
// This file is part of the Contact Form plugin for Moodle - http://moodle.org/
//
// Contact Form is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Contact Form is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Contact Form.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin for Moodle is used to send emails through a web form.
 *
 * @package    local_ourteacher
 * @copyright  2016-2021 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the Contact Form local plugin.
 *
 * @param int $oldversion - the version we are upgrading from.
 * @return bool result
 */
function xmldb_local_ourteacher_upgrade($oldversion)
{
    global $DB;
    $dbman = $DB->get_manager();
    // Moodle v3.1.0 release upgrade line.
    // Upgrade steps below.
    if ($oldversion < 2020040820) {

        // Define table teachers to be created.
        $table = new xmldb_table('teachers');

        // Adding fields to table teachers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userpicture', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('qualification', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('certificate', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table teachers.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for teachers.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Ourteacher savepoint reached.
        upgrade_plugin_savepoint(true, 2020040820, 'local', 'ourteacher');
    }
    if ($oldversion < 2025021300) {

        // Define the table we want to update.
        $table = new xmldb_table('teachers');

        // Define the new field/column with its specifications.
        // $field = new xmldb_field('approval', XMLDB_TYPE_TEXT, null, null, null, 0, null, 'description');
        $field = new xmldb_field('roleid', XMLDB_TYPE_CHAR, '250', null, XMLDB_NOTNULL, null, null, 'qualification');

        // Check if the field already exists to avoid errors.
        if (!$dbman->field_exists($table, $field)) {
            // Add the new field to the table.
            $dbman->add_field($table, $field);
        }

        // Update the plugin version to the new version.
        upgrade_plugin_savepoint(true, 2025021300, 'local', 'ourteacher');
    }
    return true;
}
