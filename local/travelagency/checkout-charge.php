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
 * TODO describe file checkout-charge
 *
 * @package    local_travelagency
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/travelagency/checkout-charge.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
echo '<pre>';
print_r($_POST);
echo '</pre>';
$name=required_param('name',PARAM_TEXT);

$address=required_param('address',PARAM_TEXT);

$phone=required_param('phone',PARAM_TEXT);

$productname=required_param('productname',PARAM_TEXT);

$productprice=required_param('productprice',PARAM_TEXT);

$stripeEmail=required_param('stripeEmail',PARAM_TEXT);

$stripeToken=required_param('stripeToken',PARAM_TEXT);
\stripe\stripe::setVerifySslCerts(false);

$stripeToken=required_param('stripeToken',PARAM_TEXT);
$data=\stripe\charge::create([
    'amount'=>$productprice,
    'currency'=>'usd',
    'description'=>'trvel agency site',
    'source'=>$stripeToken,
    
]);
echo '<pre>';
print_r($data);
echo '</pre>';
echo $OUTPUT->footer();
