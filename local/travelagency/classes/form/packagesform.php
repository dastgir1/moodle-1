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

namespace local_travelagency\form;

use moodleform;

/**
 * Class packagesform
 *
 * @package    local_travelagency
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class packagesform extends \moodleform {
    function definition()
    {
        $mform = $this->_form;
        global $CFG;
        $mform->addElement('header','packegesform',get_string('packegesform','local_travelagency'));

        $mform->addElement('text','cityname',get_string('cityname','local_travelagency'));
        $mform->settype('cityname',PARAM_TEXT);
       
        $mform->addElement('textarea', 'description', get_string("description", "local_travelagency"), 'wrap="virtual" rows="5" cols="50"');
        $mform->settype('description',PARAM_TEXT);
        $mform->addElement('text','price',get_string('price','local_travelagency'));
        $mform->settype('price',PARAM_TEXT);
        $options = array(
            'USD' => 'USD',
            'PKR' => 'PKR',
            'INR' => 'INR'
        );
        $select = $mform->addElement('select', 'currency', get_string('currency','local_travelagency'), $options);
        // This will select the colour blue.
        $select->setSelected('0');
        $mform->addElement(
            'filepicker',
            'file',
            get_string('file','local_travelagency'),
            null,
            [
                'maxbytes' => 11111111111,
                'accepted_types' => '*',
            ]
        );
        $this->add_action_buttons();
    }
    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}
