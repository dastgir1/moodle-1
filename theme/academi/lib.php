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
 * lib.php
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('FRONTPAGEPROMOTEDCOURSE', 10);
define('FRONTPAGESITEFEATURES', 11);
define('FRONTPAGEMARKETINGSPOT', 12);
define('FRONTPAGEJUMBOTRON', 13);

define('THEMEDEFAULT', 16);
define('SMALL', 15);
define('MEDIUM', 17);
define('LARGE', 18);

define('MOODLEBASED', 0);
define('THEMEBASED', 1);

define('CAROUSEL', 1);

define('EXPAND', 0);
define('COLLAPSE', 1);

define('NO', 0);
define('YES', 1);

define('SAMEWINDOW', 0);
define('NEWWINDOW', 1);

/**
 * Load the Jquery and migration files
 * @param moodle_page $page
 * @return void
 */
function theme_academi_page_init(moodle_page $page)
{
    global $CFG;
    $page->requires->js_call_amd('theme_academi/theme', 'init');
}

/**
 * Loads the CSS Styles and replace the background images.
 * If background image not available in the settings take the default images.
 *
 * @param string $css
 * @param object $theme
 * @return string
 */
function theme_academi_process_css($css, $theme)
{
    global $OUTPUT, $CFG;
    $css = theme_academi_pre_css_set_fontwww($css);
    // Set custom CSS.
    $customcss = $theme->settings->customcss;
    $css = theme_academi_set_customcss($css, $customcss);
    return $css;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 * @return string $css
 */
function theme_academi_set_customcss($css, $customcss)
{
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_academi_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = [])
{
    static $theme;
    $bgimgs = ['footerbgimg', 'loginbg'];

    if (empty($theme)) {
        $theme = theme_config::load('academi');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {

        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'footerlogo') {
            return $theme->setting_file_serve('footerlogo', $args, $forcedownload, $options);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if (preg_match("/slide[1-9][0-9]*image/", $filearea) !== false) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (in_array($filearea, $bgimgs)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Loads the CSS Styles and put the font path
 *
 * @param string $css
 * @return string
 */
function theme_academi_pre_css_set_fontwww($css)
{
    global $CFG;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot . "/theme";
    } else {
        $themewww = $CFG->themewww;
    }
    $tag = '[[setting:fontwww]]';
    $css = str_replace($tag, $themewww . '/academi/fonts/', $css);
    return $css;
}

/**
 * Load the font folder path into the scss.
 * @return string
 */
function theme_academi_set_fontwww()
{
    global $CFG;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot . "/theme";
    } else {
        $themewww = $CFG->themewww;
    }
    $fontwww = '$fontwww: "' . $themewww . '/academi/fonts/"' . ";\n";
    return $fontwww;
}


/**
 * Description
 *
 * @param string $type logo position type.
 * @return type|string
 */
function theme_academi_get_logo_url($type = 'header')
{
    global $OUTPUT;
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('academi');
    }
    if ($type == 'header') {
        $logo = $theme->setting_file_url('logo', 'logo');
        $logo = empty($logo) ? $OUTPUT->get_compact_logo_url() : $logo;
    } else if ($type == 'footer') {
        $logo = $theme->setting_file_url('footerlogo', 'footerlogo');
        $logo = empty($logo) ? '' : $logo;
    }
    return $logo;
}

/**
 *
 * Description
 * @param string $setting
 * @param bool $format
 * @return string
 */
function theme_academi_get_setting($setting, $format = true)
{
    global $CFG, $PAGE;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('academi');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        $return = $theme->settings->$setting;
    } else if ($format === 'format_text') {
        $return = format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        $return = format_text($theme->settings->$setting, FORMAT_HTML, ['trusted' => true, 'noclean' => true]);
    } else if ($format === 'file') {
        $return = $PAGE->theme->setting_file_url($setting, $setting);
    } else {
        $return = format_string($theme->settings->$setting);
    }
    return (isset($return)) ? theme_academi_lang($return) : '';
}

/**
 * Returns the language values from the given lang string or key.
 * @param string $key
 * @return string
 */
function theme_academi_lang($key = '')
{
    $pos = strpos($key, 'lang:');
    if ($pos !== false) {
        list($l, $k) = explode(":", $key);
        if (get_string_manager()->string_exists($k, 'theme_academi')) {
            $v = get_string($k, 'theme_academi');
            return $v;
        } else {
            return $key;
        }
    } else {
        return $key;
    }
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_academi_get_main_scss_content($theme)
{
    global $CFG;

    $scss = '';
    $filename = (isset($theme->settings->preset) && !empty($theme->settings->preset)) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = \context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/academi/scss/preset/default.scss');
    } else if ($filename == 'eguru') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/academi/scss/preset/eguru.scss');
    } else if ($filename == 'klass') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/academi/scss/preset/klass.scss');
    } else if ($filename == 'enlightlite') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/academi/scss/preset/enlightlite.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_academi', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Fallback to default.
        $scss .= file_get_contents($CFG->dirroot . '/theme/academi/scss/preset/default.scss');
    }
    return $scss;
}

/**
 * Get the configuration values into main scss variables.
 *
 * @param string $theme theme data.
 * @return string $scss return the scss values.
 */
function theme_academi_get_pre_scss($theme)
{
    $scss = '';
    $helperobj = new theme_academi\helper();
    $scss .= $helperobj->load_bgimages($theme, $scss);
    $scss .= $helperobj->load_additional_scss_settings();
    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_academi_get_extra_scss($theme)
{
    // Load the settings from the parent.
    $theme = theme_config::load('boost');
    // Call the parent themes get_extra_scss function.
    return theme_boost_get_extra_scss($theme);
}
/**
 * Function to get the URL of the uploaded video
 */
function theme_academi_get_video_url($homevideomedia)
{

    global $PAGE;

    $video_url = $PAGE->theme->setting_file_url('homevideomedia', 'homevideomedia');


    return $video_url;
}
function teacherlist()
{
    global $DB, $CFG, $OUTPUT;
    $teachers = $DB->get_records_sql('SELECT t.userid,t.userpic,t.roleid,u.firstname,u.lastname FROM {teachers} t JOIN {user} u  WHERE u.id= t.userid');
    $teacherdata = [];
    foreach ($teachers as $teacher) {
        $role = $DB->get_record('role', ['id' => $teacher->roleid]);
        $teacher->role = $role->shortname;
        $users = $DB->get_record('user', ['id' => $teacher->userid]);
        $usercontext = context_user::instance($users->id);

        $fs = get_file_storage();
        $files = $fs->get_area_files($usercontext->id, 'local_ourteacher', 'userpicture', $teacher->userpic);
        $file = end($files);
        if ($file->is_valid_image()) {
            // Creating picture URL.
            $teacher->picurl = moodle_url::make_pluginfile_url(
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
            $teacher->picurl = $CFG->wwwroot . '/pix/u/f1.png';
        }
        $teacherdata[] = $teacher;
    }
    return $OUTPUT->render_from_template('theme_academi/teacherlist', ['teacherdata' => array_values($teacherdata)]);
}
// function get_comments()
// {
//     global $DB, $OUTPUT;
//     $allcomments = $DB->get_records_sql("
//     SELECT 
//         c.id AS commentid, 
//         c.content AS comment_content, 
//         u.id AS userid, 
//         u.firstname, 
//         u.lastname, 
//         ra.roleid,
//         u.picture
//     FROM {comments} c
//     JOIN {user} u ON c.userid = u.id
//     JOIN {role_assignments} ra ON c.userid = ra.userid AND c.contextid=ra.contextid
// ");
//     $combinedArray = [];
//     $tempArray = [];
//     $counter = 0;

//     foreach ($allcomments as $record) {
//         // Fetch user details and role assignments.
//         $user = $DB->get_record('user', ['id' => $record->userid]);

//         $userrole = $DB->get_record('role', ['id' => $record->roleid]);

//         // Prepare individual slide data.
//         $tempArray[] = [
//             'userid' => $record->userid,
//             'comment_content' => $record->comment_content,
//             'firstname' => $record->firstname,
//             'lastname' => $record->lastname,
//             'picture' => $OUTPUT->user_picture($user, ['size' => 100]),
//             'role' => $userrole->shortname ?? 'No role',
//         ];

//         $counter++;

//         // When we have 3 records, add to the combined array and reset.
//         if ($counter == 3) {
//             $combinedArray[] = ['users' => $tempArray];
//             $tempArray = [];
//             $counter = 0;
//         }
//     }

//     // Add remaining records if any.
//     if (!empty($tempArray)) {
//         $combinedArray[] = ['users' => $tempArray];
//     }

//     // Prepare data for rendering.
//     return $OUTPUT->render_from_template('theme_academi/comments', ['slides' => $combinedArray]);
// }
// Function to get records with custom query
function theme_klass_getRecordsWithCustomQuery() {
    global $DB, $OUTPUT;

    $output = '
        <div class="container-fluid  my-5 py-4" style="background-color: #54a5dc;">
            <div class="container  ">
                <div class="text-center">
                    <h5 class="section-title  text-center text-white font-weight-bold px-3">
                        Testimonials
                    </h5>
                    <h1 class="mb-5 text-white">
                        What Our Students Say!
                    </h1>
                </div>
                
                <div class="carousel slide"  data-ride="carousel">
                    <div class="carousel-inner">'
    ;

    $records = $DB->get_records_sql(
        "SELECT c.id AS i, c.content AS comment_content,
                u.id, u.firstname, u.lastname,ra.roleid
           FROM {comments} c
           JOIN {user} u ON c.userid = u.id
           JOIN {role_assignments} ra ON c.userid = ra.userid AND c.contextid=ra.contextid
        "
    );

    // Fetch the records
    if (!empty($records)) {

        $firstrecord = array_key_first($records);
        $lastrecord = array_key_last($records);
        $i = 1;
        $active = ' active';
        $item = '';
        $items = '';
        foreach($records as $record) {
            $userrole= $DB->get_record_sql(
                "SELECT r.shortname
                   FROM {role} r
                   WHERE r.id=$record->roleid;
                   
                   
            ");
           $record->role=$userrole->shortname;
            // Get user picture.
            $picture = $OUTPUT->user_picture(core_user::get_user($record->id));

            // Collect 3 records.
            $item .= '
                        
                        <div class="col-sm-4">
								<div class="card shadow " style="
                                    padding-top: 20px;
                                    margin-bottom: 50px;
                                    border-top-left-radius: 80px;
                                    border-bottom-right-radius: 80px;
                                    background-color: #917cee;
                                    color: #fff;
                                ">
                                    <center>'.$picture.'</center>
									<div class="card-body">
										
										<h5 class="overview card-title text-center text-white" >'.$record->firstname. $record->lastname.'</h5>
										<p class="text-center "><b>'.$record->role.'</b> </p>
										<p class="card-text">'.$record->comment_content.'</p>
										
									</div>
								</div>
							</div>
            ';

            // Reseting the i to get the three values.
            if ($i > 3) {
                $i = 1;
            }

            // Check if we have three records.
            // then Put all the records in the items and reset it.
            if ($i == 3) {
                $items = $item;
                $item = ''; // Reset
            }

            if ($items != '') {                
                $output .= '
                    <div class="carousel-item' .$active.'">
                        <div class="row">'
                            .$items.
                        '</div>
                    </div>'
                ;

                $active = ''; // Unset the active variable.

                $items = ''; // Reset items.
            } else if ($lastrecord == $record->i) { // If this is the last record.
                $output .= '
                    <div class="carousel-item">
                        <div class="row">'
                            .$item.
                        '</div>
                    </div>'
                ;
            }

            $i++;
        }
    }

    $output .= '
                    </div>
                </div>
            </div>

        </div>'
    ;

    return $output;
}