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
namespace mod_externship;

/**
 * TODO describe file locallib
 *
 * @package    mod_externship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $PAGE;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../config.php');
require_once(dirname(__FILE__) . '/../../lib/outputrenderers.php');
require_once(dirname(__FILE__) . '/../../lib/accesslib.php');
require_once(dirname(__FILE__) . '/../../lib/filelib.php');
$PAGE->requires->js('/mod/externship/js/script.js');
/**
 * 
 * @param object $externship
 * @param boolean $see_all
 * @param boolean $editing
 */
require_login();
class locallib
{
    function externship_get_list($externship, $see_all, $editing = true)
    {
        global $OUTPUT, $DB, $USER;
        $coursemoduleid = required_param('id', PARAM_INT);

        $course_module = $DB->get_record('course_modules', ['id' => $coursemoduleid]);

        $externships = $DB->get_records('externship', ['course' => $course_module->course]);


        $first_row = true;
        $result = <<<EOD
    <table id="externship_list_table" cellpadding="5" rules="rows" frame="below">
    <col width="50" />
    
    <input type="search" class="border rounded p-2" id="search" placeholder="Search here..."/>
     
    EOD;


        $rows = $DB->get_records('externship_data', array('cmid' => $coursemoduleid), 'starttime DESC');

        if (empty($rows))
            return "<div style=\"text-align:center\"><a class='btn btn-primary rounded float-right' href=\"externshipform.php?externshipid=$externship->id\">$add_new_string</a></div>";
        foreach ($rows as $row) {
            if ((!$see_all) && ($USER->id != $row->userid)) continue;
            if ($first_row) $result .= externship_get_list_table_title($row, $editing);
            $result .= externship_get_list_table_row($row, $editing);
            $first_row = false;
        }
        $result .= "</table>\n";
        return $result;
    }



    function externship_get_list_table_title($row, $editing)
    {

        $result = "\t<thead>\n";
        $result .= "\t<tr>\n";
        if ($editing) $result .= "\t\t<th>" . get_string("actions", 'externship') . "</th>\n"; // editing cell title: blank
        foreach ($row as $name => $data) {
            if (in_array($name, array('id', 'externshipid'))) continue;
            switch ($name) {

                case 'userid':
                    $result .= "\t\t<th>" . get_string("username", 'externship') . "</th>\n";
                    break;
                case 'date':
                    $result .= "\t\t<th>" . get_string("date", 'externship') . "</th>\n";
                    break;

                case 'description':
                    $result .= "\t\t<th>" . get_string("description", 'externship') . "</th>\n";
                    break;
                default:

                    $result .= "\t\t<th>" . get_string($name, 'externship') . "</th>\n";
            }
        }
        $result .= "\t\t<th>" . get_string('status', 'externship') . "</th>\n";
        $result .= "\t\t<th>" . get_string('comments', 'externship') . "</th>\n";
        $result .= "\t\t<th>" . get_string('file', 'externship') . "</th>\n";
        $result .= "\t\t<th>" . get_string('permission', 'externship') . "</th>\n";
        $result .= "\t</tr>\n";
        $result .= "\t</thead>\n";
        return $result;
    }

    function externship_get_list_table_row($row, $editing)
    {
        global $OUTPUT, $USER, $COURSE, $DB, $modinfo;

        // Get cmid and course id
        $cmid = optional_param('id', 0, PARAM_INT);
        $courseid = $DB->get_record_sql("SELECT course FROM {course_modules} WHERE id = ?", array($cmid));

        // Get course context
        $context = context_course::instance($courseid->course);
        $student_role_id = 5;
        // Start building the table row
        // $result = "\t<tbody id='entrytable'>\n";
        $result = "\t<tr>\n";

        // If editing is enabled, show edit and delete icons
        if ($row->approval == 0) {
            $result .= "\t\t<td>";

            $result .= '<a  href="externshipform.php?dataid=' . $row->id . '" class="mx-1"><i class="fa fa-gear"></i></a>';
            $result .= '<a  href="delete.php?dataid=' . $row->id . '"><i class="fa fa-remove"></i></a>';

            $result .= "</td>\n";
        } else {
            if (user_has_role_assignment($USER->id, $student_role_id, $context->id)) {
                $result .= "\t\t<td>";
                $result .= '';
                $result .= "</td>\n";
            } else {

                if (has_capability('mod/externship:canapproveentries', $context)) {
                    $result .= "\t\t<td>";

                    $result .= '<a  href="externshipform.php?dataid=' . $row->id . '" ><i class="fa fa-gear "></i></a>';
                    $result .= '<a  href="delete.php?dataid=' . $row->id . '" ><i class="fa fa-remove"></i></a>';

                    $result .= "</td>\n";
                }
            }
        }


        // Loop through the row data
        foreach ($row as $name => $data) {
            if (in_array($name, array('id', 'externshipid'))) continue;
            switch ($name) {

                case 'userid':
                    $user = $DB->get_record('user', array('id' => $data));
                    $result .= "\t\t<td>" . fullname($user) . "</td>\n";
                    break;

                case 'date':
                    $newdate = date('D d M Y', $data);
                    $result .= "\t\t<td>$newdate</td>\n";
                    break;
                case 'starttime':
                    $sdate = date('H:i A', $data);
                    $result .= "\t\t<td>$sdate</td>\n";
                    break;
                case 'endtime':
                    $edate = date('H:i A', $data);
                    $result .= "\t\t<td>$edate</td>\n";
                    break;
                case 'duration':
                    $date = format_time($data);
                    $result .= "\t\t<td>$date</td>\n";
                    break;
                case 'description':

                    $result .= "\t\t<td>$row->description</td>\n";
                    break;


                default:
                    $result .= "\t\t<td>$data</td>\n";
            }
        }

        // Adding the approval status (Approved / Not Approved)
        $result .= "\t\t<td>";
        if ($row->approval == 0) {
            // If $data is 0, show "Not Approved"
            $result .= 'Not Approved';
        } else {
            // If $data is not 0, show "Approved"
            $result .= 'Approved';
        }

        $result .= "</td>\n";

        if (has_capability('mod/externship:canapproveentries', $context)) {
            $result .= "\t\t<td>";
            $result .= '<form action="add_comment.php?id=' . $row->id . '&cmid=' . $cmid . '" method="post">';
            $result .= "<textarea  name='comment' class='border rounded d-block my-2' placeholder='Enter comment' rows='1' cols='14'></textarea>";
            $result .= '<button type="submit" class="btn btn-primary rounded text-truncate" style="max-width: 150px;" name="submit" value="' . $row->id . '">Add Comment</button>';

            $result .= '</form>';
            $comment = $DB->get_record('externship_data', ['id' => $row->id]);
            $result .= '' . $comment->comments . '';
            $result .= "</td>\n";
        } else {
            $comment = $DB->get_record('externship_data', ['id' => $row->id]);

            $result .= "\t\t<td>";
            $result .= "<div id='showcomment' >$comment->comments</div>";

            $result .= "</td>\n";
        }



        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $externship = $DB->get_record('externship', array('id' => $row->externshipid), '*', MUST_EXIST);
        $course         = $DB->get_record('course', array('id' => $externship->course), '*', MUST_EXIST);
        $course = new core_course_list_element($course);

        $cm             = get_coursemodule_from_instance('externship', $externship->id, $course->id, false, MUST_EXIST);
        $cm_context = context_module::instance($cm->id);
        $fs = get_file_storage();
        // $files = $fs->get_area_files($cm_context->id , 'mod_externship', 'file',$row->id);
        $files = $fs->get_area_files($cm_context->id, 'mod_externship', 'file', $row->id, 'sortorder DESC', false);


        $download_url = '';
        foreach ($files as $file) {
            // Check if the file is not the directory placeholder ('.').
            if ($file->get_filename() !== '.') {
                // Generate the download URL using Moodle's pluginfile.php.
                $download_url = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                )->out();  // Convert to URL string.

                // Print the download URL or do any other processing.

            }
        }



        $result .= "\t\t<td>";
        $extension = pathinfo($download_url, PATHINFO_EXTENSION); // Get the file extension

        // Set default variables for icon and preview
        $icon = '';
        $preview = '';

        if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
            $icon = $download_url; // Use the image itself as the icon
            $preview = 'preview'; // Set preview attribute for images
        } elseif ($extension == 'pdf') {
            $icon = '/mod/externship/pix/pdf.jpg'; // Path to your PDF icon
            $preview = ''; // No preview for PDF
        } elseif (in_array($extension, ['doc', 'docx'])) {
            $icon = '/mod/externship/pix/word (1).jpg'; // Path to your Word icon
            $preview = ''; // No preview for Word document
        }

        $result .= '<a href="' . $download_url . '" ' . $preview . '>
            <img src="' . $icon . '" alt="Download Image" width="50" height="50"/>
        </a>';
        $result .= "</td>\n";
        // Adding the approve button conditionally based on the capability
        $result .= "\t\t<td>";
        if (has_capability('mod/externship:canapproveentries', $context)) {
            // Show button if user has the capability
            if ($row->approval == 0) {
                // Show button if user has the capability and data is not 1
                $result .= '<button type="button" id="submit" class="rounded btn btn-primary" onclick="location.href=\'/mod/externship/externship_permission.php?dataid=' . $row->id . '\'">Approve</button>';
            } else {
                $result .= '<button type="button" id="submit" class="rounded btn btn-danger" onclick="location.href=\'/mod/externship/disapprove.php?dataid=' . $row->id . '\'">Disapprove</button>';
            }
        }
        $result .= "</td>\n";



        // Close the table row

        $result .= "\t</tr>\n";
        // $result .= "\t</tbody>\n";

        return $result;
    }
}
