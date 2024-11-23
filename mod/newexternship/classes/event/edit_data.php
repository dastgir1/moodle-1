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

namespace mod_newexternship\event;

/**
 * The edit_data event class.
 *
 * @package     mod_newexternship
 * @category    event
 * @copyright   2024 Dastgir<ghulam.dastgir@paktaleem.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_data extends \core\event\base {

    // For more information about the Events API please visit {@link https://docs.moodle.org/dev/Events_API}.
         
    protected function init() {
        $this->data['crud'] = 'u'; // 'u' for update
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'newexternship';
    }
    
    public static function get_name() {
        return get_string('eventeditdata', 'newexternship');
    }
    
    public function get_description() {
        return "The user with id '{$this->userid}' updated the newexternship data with id '{$this->objectid}' in the course with id '{$this->courseid}'.";
    }
    
    public function get_url() {
        return new \moodle_url('/mod/newexternship/newexternshipform.php', array('dataid' => $this->objectid, 'newexternshipid' => $this->get_data()['other']['newexternshipid']));
    }
}
