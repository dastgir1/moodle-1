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
 * Form for editing our teacher  instances.
 * @copyright  2024  {@link http://paktaleem.com}
 * @package  local_ourteacher
 * @author    paktaleem
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
function local_ourteacher_pluginfile(
    $course,
    $cm,
    $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {
    // global $DB;

    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    // if ($context->contextlevel != CONTEXT_MODULE) {
    //     return false;
    // }

    // Make sure the filearea is one of those used by the plugin.
    // if ($filearea !== 'expectedfilearea' && $filearea !== 'anotherexpectedfilearea') {
    //     return false;
    // }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    // require_login($course, true, $cm);

    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    // if (!has_capability('local/ourteacher:view', $context)) {
    //     return false;
    // }

    // The args is an array containing [itemid, path].
    // Fetch the itemid from the path.
    // $itemid = array_shift($args);

    // The itemid can be used to check access to a record, and ensure that the
    // record belongs to the specifeid context. For example:
    // if ($filearea === 'expectedfilearea') {
    //     $post = $DB->get_record('local/ourteacher_posts', ['id' => $itemid]);
    //     if ($post->myplugin !== $context->instanceid) {
    //         // This post does not belong to the requested context.
    //         return false;
    //     }

    //     // You may want to perform additional checks here, for example:
    //     // - ensure that if the record relates to a grouped activity, that this
    //     //   user has access to it
    //     // - check whether the record is hidden
    //     // - check whether the user is allowed to see the record for some other
    //     //   reason.

    //     // If, for any reason, the user does not hve access, you can return
    //     // false here.
    // }

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

    $file = $fs->get_file($context->id, 'local_ourteacher', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        // The file does not exist.
        return false;
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file);
}

// Function to retrieve teacher records from the Moodle database
/**
 * [Description for getTeacherRecords]
 * @package  local_ourteacher
 * @return [type]
 * 
 */

function getTeacherRecords()
{
    global $DB, $CFG;

    $result = $DB->get_records_sql("
        SELECT t.userid, t.qualification, t.userpic, u.firstname, u.lastname, e.courseid
        FROM {teachers} t
        JOIN {user} u ON t.userid = u.id
        JOIN {user_enrolments} ue ON ue.userid = t.userid AND ue.status = 0
        JOIN {enrol} e ON e.id = ue.enrolid
       
    ");

    $teachersdata = array();

    // Generate the User profile pic URL.
    foreach ($result as $r) {

        $fs = get_file_storage();
        $context = context_user::instance($r->userid);
        $files = $fs->get_area_files($context->id, 'user', 'icon', '/', 0, $r->userpic);
        $file = end($files);

        if ($file) {
            // Creating picture URL.
            $r->picurl = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                false
            )->out();
        } else {
            // Default picture URL or handle missing picture.
            $r->picurl = $CFG->wwwroot . '/pix/u/f1.png';
        }

        $r->link = $CFG->wwwroot . '/course/info.php?id=' . $r->courseid;
        $teachersdata[] = $r;
    }

    return $teachersdata;
}
