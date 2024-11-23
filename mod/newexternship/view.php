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
 * Prints an instance of mod_newexternship.
 *
 * @package     mod_newexternship
 * @copyright   2024 Dastgir<ghulam.dastgir@paktaleem.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->libdir.'/accesslib.php');
// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$n = optional_param('n', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('newexternship', $id, 0, false, MUST_EXIST);
  
   $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('newexternship', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('newexternship', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('newexternship', $moduleinstance->id, $course->id, false, MUST_EXIST);
}
$newexternship = $DB->get_record('newexternship', array('course' => $course->id), '*', MUST_EXIST);
require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_newexternship\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('newexternship', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/newexternship/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();
$addnewrecord =get_string('addnewrecord','newexternship');
echo '<a href="/mod/newexternship/newexternshipform.php?newexternshipid='.$moduleinstance->id.'" ><button type="submit" class="btn btn-primary">'.$addnewrecord.'</button></a>';
$rows = $DB->get_records('newexternship_data', array('cmid' => $id,'newexternshipid'=>$newexternship->id), 'starttime DESC');

$table ='';
if($rows!=''){
    $table .='<div class="container">';
    $table .='<div class="row">';
    $table .='<div class="col-md-12">';
    $table .='<div class="card">';
    $table .='<div class="card-heaser">';
     $table .='<h3 class="text-center">'.$moduleinstance->name.'</h3>';
    $table .='</div>';
    $table .='<div class="crd-body">';
    $table .='<table class="table table-striped">';
    $table .='<thead>';
    $table .='
    <tr>
        <th>Action</th>
        <th>ID</th>
        <th>User Name</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Duration</th>
        <th>Descripion</th>
        <th>Clinic Name</th>
        <th>Preceptor Name</th>
        <th>Status</th>
        <th>Comments</th>
        <th>File</th>
        <th></th>
    </tr>
    ';
    $table .='</thead>';
    $table .='<tbody>';
    foreach($rows as $row){
        $user = $DB->get_record('user',['id'=>$row->userid]);
        $username = $user->firstname.' '.$user->lastname;
        $starttime = date('D d M Y H:i A',$row->starttime);
        $endtime = date('D d M Y H:i A',$row->endtime);
        $durationhour     = intval(intval($row->duration) / 3600);
  
        $durationminute   = intval((intval($row->duration) - $durationhour * 3600) / 60);
        $duration=$durationhour.' Hours '. $durationminute.' Minutes';
        $table .= '<tr>';
        $context = context_course::instance($course->id); 
        if (has_capability('mod/newexternship:canapproveentries', $context)){
            $table .= "<td>";
            
            $table .= '<a   href="newexternshipform.php?dataid=' . $row->id . '" ><i class="fa fa-gear "></i></a>';
            $table .= '<a id="deleterecord" href="/mod/newexternship/delete.php?dataid=' . $row->id . '" value="' . $row->id . '"><i class="fa fa-remove"></i></a>';
        
            $table .= "</td>";
        }
        $table .= '
    
        <td>'.$row->id.'</td>
        <td>'.$username.'</td>
        <td>'.$starttime.'</td>
        <td>'.$endtime.'</td>
        <td>'.$duration.'</td>
        <td>'.$row->description.'</td>
        <td>'.$row->clinicname.'</td>
        <td>'.$row->preceptorname.'</td>
        ';
         $table .= '<td>';
        if ($row->approval == 0) {
            // If $data is 0, show "Not Approved"
            $table .= 'Not Approved';
        } else {
            // If $data is not 0, show "Approved"
            $table .= 'Approved';
        }
        $table .= '</td>';
        if (has_capability('mod/newexternship:canapproveentries', $context)){
            $table .= "<td>";
            $table .='<form action="add_comment.php?dataid=' . $row->id . '&cmid='.$row->cmid .'" method="post">';
            $table .= "<textarea  name='comment' class='border rounded d-block my-2' placeholder='Enter comment' rows='1' cols='14'></textarea>";
            $table .= '<button type="submit" class="btn btn-primary " name="submit" value="' . $row->id . '">Comment</button>';
            $table .='</form>';
            $comment = $DB->get_record('newexternship_data',['id'=>$row->id]);
             $table .=''.$comment->comments.'';
            $table .= "</td>";
        }else{
            $comment = $DB->get_record('newexternship_data',['id'=>$row->id]);
            // print_object($comment->comments);
            $table .= "<td>";
            $table .= "<div id='showcomment' >$comment->comments</div>";
        
            $table .= "</td>";
        }
        $cm_context = context_module::instance($id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($cm_context->id , 'mod_newexternship', 'file',$row->id);
        $file= end($files);
   
        if ($file && !$file->is_directory()) {
           
            $download_url = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            )->out();
        } else {
            // Default picture URL or handle missing picture.
            $download_url = null;
        }
            $table .= "<td>";
                $table .='<a href="'.$download_url.'" preview="">
                    <img src="'.$download_url.'" alt="Download Image" width="50" height="50"/>
                </a>';
            $table .= "</td>";
            $table .= "<td>";
            if (has_capability('mod/newexternship:canapproveentries', $context)) {
                // Show button if user has the capability
                if ($row->approval == 0) {
                    // Show button if user has the capability and data is not 1
                    $table .= '<a href=/mod/newexternship/approve.php?dataid='.$row->id.'"><button type="submit" id="submit" class="rounded btn btn-primary" >Approve</button></a>';
                }else{
                    $table .= '<a href=/mod/newexternship/disapprove.php?dataid='.$row->id.'"><button type="submit" id="submit" class="rounded btn btn-primary" >Disapprove</button></a>';
                }
                
            }
            $table .= "</td>";
        $table .= '</tr>';
    }
    $table .='</tbody>';
    $table .='</table>';
    $table .='</div>';
    $table .='</div>';
    $table .='</div>';
    $table .='</div>';
    $table .='</div>';
    echo $table;
}else{
    echo 'Record Not Found';
}
echo $OUTPUT->footer();
