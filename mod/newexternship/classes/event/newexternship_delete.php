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

namespace mod_newexternship\event;

/**
 * Event newexternship_delete
 *
 * @package    mod_newexternship
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newexternship_delete extends \core\event\base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'newexternship';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
      
    }
    
    /**
     * Returns the event name.
     *
     * @return string Event name.
     */
    public static function get_name() {
        return get_string('eventnewexternshipdeleted', 'mod_newexternship');
    }

    /**
     * Returns a description of the event.
     *
     * @return string Description of the event.
     */
    public function get_description() {
        return "The new externship '{$this->other['newexternship_name']}' with ID {$this->objectid} was deleted by user with ID {$this->userid}.";
    }

    /**
     * Returns the relevant URL.
     *
     * @return \moodle_url URL to view the externship.
     */
    public function get_url() {
        return new \moodle_url('/mod/newexternship/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Legacy log support.
     *
     * @return array Legacy log data.
     */
    protected function get_legacy_logdata() {
        return array(
            $this->courseid,                         // Course ID
            'newexternship',                            // Module name
            'delete',                                // Action
            "view.php?id={$this->contextinstanceid}", // URL
            $this->objectid,                         // Externship ID
            $this->contextinstanceid                 // Context module ID
        );
    }

    /**
     * Custom validation for event data.
     *
     * @throws \coding_exception If validation fails.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['newexternship_name'])) {
            throw new \coding_exception('The externship_name must be set in other.');
        }
    }
}
