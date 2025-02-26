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
 * Callback implementations for local_comments_slider
 *
 * @package    local_comments_slider
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Serve the files from the myplugin file areas.
 * @package  local_ourteacher
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function local_comments_slider_pluginfile(
    $course,
    $cm,
    $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {


    // For a plugin which does not specify the itemid, you may want to use the following to keep your code consistent:
    // $itemid = null;
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.
    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (empty($args)) {
        // $args is empty => the path is '/'.
        $filepath = '/';
    } else {
        // $args contains the remaining elements of the filepath.
        $filepath = '/' . implode('/', $args) . '/';
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'local_comments_slider', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        // The file does not exist.
        return false;
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file);
}
// Function to retrieve Comments records from the Moodle database
/**
 * [Description for getCommentsRecords]
 * @package  local_comments_slider
 * @return [type]
 * 
 */

function getCommentsRecords()
{
    global $DB, $CFG, $OUTPUT;

    $result = $DB->get_records_sql("
        SELECT c.id AS i, c.content AS comment_content,
                u.id, u.firstname, u.lastname,ra.roleid
           FROM {comments} c
           JOIN {user} u ON c.userid = u.id
           JOIN {role_assignments} ra ON c.userid = ra.userid AND c.contextid=ra.contextid
       
    ");

    $commentsdata = array();

    // Generate the User profile pic URL.
    foreach ($result as $r) {

        $userrole = $DB->get_record_sql(
            "SELECT r.shortname
                   FROM {role} r
                   WHERE r.id=$r->roleid;
                   
                   
            "
        );
        $r->role = $userrole->shortname;
        // Get user picture.
        $r->picture = $OUTPUT->user_picture(core_user::get_user($r->id));



        $commentsdata[] = $r;
    }

    return $OUTPUT->render_from_template('local_comments_slider/comments_slider', ['commentsdata' => array_values($commentsdata)]);
}
