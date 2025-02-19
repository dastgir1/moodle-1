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

namespace local_ourteacher;

/**
 * Class newteacher
 *
 * @package    local_ourteacher
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newteacher extends \moodleform
{
    /**
     *
     * The definition() function defines the form elements.
     *
     */
    public function definition()
    {

        global $DB, $CFG, $PAGE, $USER, $context, $instance, $imsid;

        $mform = $this->_form;
        $mform->addElement('header', 'newteacherform', get_string('newteacherform', 'local_ourteacher'));

        /* TEXTBOX (HIDDEN)
		   userid:Userid
		   Rule types: No rules (optional).
		 */
        $mform->addElement('hidden', 'imsid');
        $mform->setType('imsid', PARAM_INT);
        $mform->setDefault('imsid', $imsid);
        // Email
        $mform->addElement('text', 'email', get_string("email", "local_ourteacher"), 'wrap="virtual" rows="5" cols="5"', array('maxlength' => '700'));
        $mform->setType('email', PARAM_TEXT);
        $mform->addRule('email', get_string('required', 'local_ourteacher'), 'required', null, 'client');
        $mform->addHelpButton('email', 'email', 'local_ourteacher');

        // Firstname
        $mform->addElement('text', 'firstname', get_string("firstname", "local_ourteacher"), 'wrap="virtual" rows="5" cols="5"', array('maxlength' => '700'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required', 'local_ourteacher'), 'required', null, 'client');
        $mform->addHelpButton('firstname', 'firstname', 'local_ourteacher');

        // Lastname
        $mform->addElement('text', 'lastname', get_string("lastname", "local_ourteacher"), 'wrap="virtual" rows="5" cols="5"', array('maxlength' => '700'));
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required', 'local_ourteacher'), 'required', null, 'client');
        $mform->addHelpButton('lastname', 'lastname', 'local_ourteacher');
        //Qualifications
        $mform->addElement('textarea', 'qualification', get_string("qualification", "local_ourteacher"), 'wrap="virtual" rows="5" cols="5"', array('maxlength' => '700'));

        $mform->setType('qualification', PARAM_TEXT);
        $mform->addRule('qualification', get_string('required', 'local_ourteacher'), 'required', null, 'client');
        $mform->addHelpButton('qualification', 'qualification', 'local_ourteacher');


        /* UPLOAD FILE (IMAGE FORMAT)
		   userpic:User picture.
           Rule types: User has to upload an image.
         */
        $maxbytes = get_max_upload_sizes();
        $mform->addElement('filepicker', 'userpicture', get_string('userpicture', 'local_ourteacher'), null, ['maxbytes' => $maxbytes, 'accepted_types' => '*',]);
        $mform->addRule('userpicture', get_string('required', 'local_ourteacher'), 'required', null, 'client');
        $mform->setType('image/jpg', PARAM_RAW);
        $mform->addHelpButton('userpicture', 'userpicture', 'local_ourteacher');


        // Action buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('submit', 'local_ourteacher'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', '', false);
    }
    /**
     *
     * The validation() function defines the form validation.
     *
     * @param My_Type $data
     * @param My_Type $files
     */
    public function validation($data, $files)
    {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
