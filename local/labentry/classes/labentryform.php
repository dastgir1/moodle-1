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

namespace local_labentry;

/**
 * Class labentryform 
 *
 * @package    local_labentry
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class labentryform extends \moodleform {
    // Add elements to form.
    public function definition() {

        global $CFG;
       
        $mform = $this->_form; // Don't forget the underscore!
      
        
        $mform->addElement(
            'filemanager',
            'file',
            get_string('file', 'local_labentry'),
            null,
            [
                'subdirs' => 0,
                'maxbytes' => $CFG->maxbytes,
                'areamaxbytes' => 10485760,
                'maxfiles' => 1,
                'accepted_types' => array('.pdf','.doc', '.docx', '.text'),
                // 'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
            ]
        );
       
        // submit button
        $this->add_action_buttons();

    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}