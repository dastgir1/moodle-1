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

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $ADMIN->add('root', new admin_category('teacher_signup', get_string('pluginname', 'local_ourteacher')));
	
	$ADMIN->add('teacher_signup', new admin_externalpage('teachersignup', get_string('ourteacher', 'local_ourteacher'),
                 new moodle_url('/local/ourteacher/ourteacher.php')));

    $ADMIN->add('teacher_signup', new admin_externalpage('usermetadata', get_string('usermetadata', 'local_ourteacher'),
                 new moodle_url('/local/teacher_signup/metadata.php')));			 
}