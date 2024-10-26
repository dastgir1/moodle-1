<?php

/**
 * @package    mod
 * @subpackage dataentry
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
';
require_once(dirname(__FILE__).'/lib.php');


$PAGE->requires->js('/mod/dataentry/js/index.js');
require_once($CFG->libdir.'/accesslib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // offlinesession instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('dataentry', $id, 0, false, MUST_EXIST);

    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $dataentry  = $DB->get_record('dataentry', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $dataentry  = $DB->get_record('dataentry', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $dataentry->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('dataentry', $dataentry->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
// $modulecontext = context_module::instance($cm->id);
// $event = \mod_dataentry\event\course_module_viewed::create(array(
//     'objectid' => $moduleinstance->id,
//     'context' => $modulecontext
// ));
require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$see_all = has_capability('mod/dataentry:manageall', $context);
$modinfo = get_fast_modinfo($course);

$PAGE->set_url('/mod/dataentry/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($dataentry->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();


global $PAGE;

if (has_capability('mod/dataentry:canapproveentries', $context)) {
    $cmid =optional_param('id', 0, PARAM_INT); // Get the course module ID.

    echo "
    <ul class='nav nav-tabs'>
        <li class='nav-item border rounded'>
            <a class='nav-link' href='/mod/dataentry/approvedentries.php?id=$cmid'>Approved Entries</a>
        </li>
     
    </ul>
    ";
}
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 2, PARAM_INT);
$totalcount = $DB->count_records_select('dataentry_data', 'cmid = ?', array($cmid));
$start = $page * $perpage;
if ($start > $totalcount) {
    $page = 0;
    $start = 0;
}
$add_new_string = get_string('adddataentry', 'dataentry');
echo '<caption><a href="dataentryform.php?dataentryid='.$dataentry->id.'" class="btn btn-primary rounded mt-3 " style="float:right;">'.$add_new_string.'</a></caption>';
echo '<input type="search" class="border rounded mt-3 p-2" id="search" placeholder="Search Here..."/>';
if (has_capability('mod/dataentry:canapproveentries', $context)) {

    $datarecords = $DB->get_records('dataentry_data', array('cmid' => $id), 'starttime DESC', '*', $start, $perpage);
}else{
    $datarecords = $DB->get_records('dataentry_data', array('cmid' => $id,'userid'=>$USER->id), 'starttime DESC', '*', $start, $perpage);

}
 // Assuming you pass course module ID in URL

// Get the module context
$context = context_module::instance($id);
// $courseid = $DB->get_record_sql("SELECT course FROM {course_modules} WHERE id=$cmid");
// print_object($courseid->course);
$student_role_id = 5; // Moodle's default student role ID

if (user_has_role_assignment($USER->id, $student_role_id, $context->id)) {
    // Fetch the data from the database
    global $USER; // Ensure the global user object is accessible
    $userid = $USER->id; // Get the logged-in user's ID
    
    $timerecords = $DB->get_records_sql("
        SELECT cm.course, SUM(od.duration) AS total_duration
        FROM {dataentry_data} od
        JOIN {course_modules} cm ON od.cmid = cm.id
        WHERE cm.id = :cmid AND od.approval = 1 AND od.userid = :userid
        GROUP BY cm.course
    ", [
        'cmid' => $id,
        'userid' => $userid // Bind the logged-in user's ID
    ]);

    // Display the data in an HTML table
    if ($timerecords) {
        foreach ($timerecords as $entry) {
            $duration = ($entry->total_duration/60)/60;
            $total_duration=$duration . ' Hours'; 
            echo 'dataentry Total Hours: ' . $total_duration ;           
        }

    } else {
        echo '<p>No entries approved.</p>';
    }
} else {

}
$context = context_module::instance($cm->id);

// Check if the intro field exists and rewrite its URLs.
// if (isset($dataentry->intro)) {
//     $description = file_rewrite_pluginfile_urls($dataentry->intro, 'pluginfile.php', $context->id, 'mod_dataentry', 'intro', $dataentry->id);
//     echo format_text($description, FORMAT_HTML);
// } else {
//     echo "No description available.";
// }

if ($dataentry->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('dataentry', $dataentry, $cm->id), 'generalbox mod_introbox', 'dataentryintro');
}

// $content = dataentry_get_list($dataentry, $see_all);

// echo $OUTPUT->box($content, 'generalbox');
$table = '';
if(!empty($datarecords)) {
    $table .='<div class="container-fluid mt-5">';
    $table .='<div class="row">';
    $table .='<div class="col-md-12">';
    $table .='<div class="card">';
    $table .='<div class="card-header">';
    $table .= '<h3 class="text-center">'.$dataentry->name.'</h3>';
    $table .='</div>';
    $table .='<div class="card-body">';
    $table .= '<table class="table table-striped ">';
    $table .= '<thead>';
    $table .= '
    <tr>
        <th scope="col">Action</th>
        <th scope="col">#ID</th>
        <th scope="col">user Name</th>
        <th scope="col">Start Time</th>
        <th scope="col">End Time</th>
        <th scope="col">Duration</th>
        <th scope="col">Description</th>
        <th scope="col">Clinic Name</th>
        <th scope="col">Preceptor Name</th>
        <th scope="col">Status</th>
        <th scope="col">Comment</th>
        <th scope="col">File</th>
        <th scope="col"></th>
    </tr>
    ';
    $table .= '</thead>';
    $table .= '<tbody>';
    
    $table .= '</tbody id="datatable">';
    foreach ($datarecords as $datarecord) {
        $starttime = date('D d M Y H:i A', $datarecord->starttime);
        $endtime = date('D d M Y H:i A', $datarecord->endtime);
        $durationhours = floor(($datarecord->duration) / 3600) % 60;
        $durationminutes = ($datarecord->duration / 60) % 60;
        $duration = $durationhours . ' Hours ' . $durationminutes . ' Minutes';

        $context = context_module::instance($id);
        $table .= "<tr>";
        $table .= "<td>";
        if (has_capability('mod/dataentry:canapproveentries', $context)) {

            $table .= '<a href="dataentryform.php?dataid=' . $datarecord->id . '" ><button type="button" class="btn-primary" ><i class="fa fa-gear"></i></button></a>
             <button type="button" class=" btn-danger  del-btn" value="' . $datarecord->id . '"> <i
                                                class="fa fa-remove"></i></button>';
        } else {
            $table .= '';
        }
        $table .= " </td>";
        $user = $DB->get_record('user', ['id' => $datarecord->userid]);
        $table .= '
        <td>' . $datarecord->id . '</td>
        <td>' . $user->firstname . ' ' . $user->firstname . '</td>
        <td>' . $starttime . ' </td>
        <td>' . $endtime . ' </td>
        <td>' . $duration . ' </td>
        <td>' . $datarecord->description . ' </td>
        <td>' . $datarecord->clinicname . ' </td>
        <td>' . $datarecord->preceptorname . ' </td>
        ';
        if ($datarecord->approval == 0) {

            $table .= '
            <td>Not Approve</td>
            ';
        } else {
            $table .= '
            <td>Approve</td>
            ';
        }
        $table .= '<td>';
        if (has_capability('mod/dataentry:canapproveentries', $context)) {
            $table .= '
            <form action="comments.php?dataid=' . $datarecord->id . '&cmid=' . $cmid . '" method="post">
            <textarea type="text" name="comment" class="form-control" rows="1" cols="10"></textarea>
            <button type="submit" name="submit" class="btn btn-primary mt-2">Submit</button>
            </form>
            ' . $datarecord->comments . '
            ';
        } else {
            $table .= '' . $datarecord->comments . '';
        }

        $table .= '</td>';
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_dataentry', 'file', $datarecord->id);
 
        foreach($files as $file){
            if($file->get_filename()!='.'&& !$file->is_directory()){
                $download_url = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                )->out();
            }
        }
            // print_object($download_url);
        // $file = end($files);


        // if ($file && !$file->is_directory()) {

        //     $download_url = moodle_url::make_pluginfile_url(
        //         $file->get_contextid(),
        //         $file->get_component(),
        //         $file->get_filearea(),
        //         $file->get_itemid(),
        //         $file->get_filepath(),
        //         $file->get_filename()
        //     )->out();
        // } else {
        //     // Default picture URL or handle missing picture.
        //     $download_url = null;
        // }
     
        $table .= '<td><a href="' . $download_url . '" preview="">
            <img src="' . $download_url . '" alt="Download Image" width="50" height="50"/>
        </a></td>';
        if (has_capability('mod/dataentry:canapproveentries', $context)) {

            if ($datarecord->approval == 0) {
                $table .= '<td><a href="approve.php?dataid=' . $datarecord->id . '&cmid=' . $cmid . '">
                <button  type="submit" class="btn btn primary">Approve</button>
            </a></td>';
            } else {
                $table .= '<td><a href="disapprove.php?dataid=' . $datarecord->id . '&cmid=' . $cmid . '" >
                <button  type="submit" class="btn btn primary"> Disapprove</button>
            </a></td>';
            }
        } else {
            $table .= '';
        }
        $table .= "</tr>";
    }
    $table .= '</table>';
    $table .='</div>';
    $table .='</div>';
    $table .='</div>';
    $table .='</div>';
    $table .='</div>';
    

    echo  $table;
    $baseurl = new moodle_url('/mod/dataentry/view.php?id=' . $cmid . '');
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
    
}
// $instanceid =$this->get_new_parentid('dataentry');
// Get course module ID (cmid) based on instance ID and module type ('dataentry')

// Finish the page
echo $OUTPUT->footer();
