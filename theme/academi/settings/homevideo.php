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
 * TODO describe file homevideo
 *
 * @package    theme_academi
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// Video uploading.
$temp = new admin_settingpage('theme_academi_homevideo', get_string('homevideo', 'theme_academi'));

// Video heading.
$name = 'theme_academi_homevideoheading';
$heading = get_string('homevideoheading', 'theme_academi');
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$temp->add($setting);

// Enable or disable option for Video .
$name = 'theme_academi/homevideostatus';
$title = get_string('status', 'theme_academi');
$description = get_string('statusdesc', 'theme_academi');
$default = NO;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);

// Video Title.
$name = 'theme_academi/homevideo';
$title = get_string('title', 'theme_academi');
$description = get_string('titledesc', 'theme_academi');
$default = 'lang:video';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$temp->add($setting);

// Video Media.
$name = 'theme_academi/homevideomedia';
$title = get_string('media', 'theme_academi');
$description = get_string('mspotmedia_desc', 'theme_academi');

// Video uploader setting
$setting = new admin_setting_configstoredfile(
    $name,
    $title,
    $description,
    'homevideomedia',
    0,
    ['accepted_types' => ['video']]
);

$temp->add($setting);
$settings->add($temp);
