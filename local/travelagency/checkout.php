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
 * TODO describe file checkout
 *
 * @package    local_travelagency
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/local/travelagency/checkout.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$id = required_param('id',PARAM_INT);
$place = $DB->get_record('packages',['id'=>$id]);

?>

<button type="button" onclick="goback()" class="back" >Go Back</button>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">User Form</h3>
                </div>
                <div class="card-body">
                    <form action="checkout-charge.php" method="post">
                        <div class="form-group">
                            <label for="" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Name" required>
                        </div>
                       
                        <div class="form-group">
                            <label for="" class="form-label">Customer Address</label>
                            <input type="text" class="form-control" name="address" placeholder="Enter Address" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Customer Phone No</label>
                            <input type="number" class="form-control" name="phone" placeholder="Enter Phone No"pattern="/d{10}" maxlength="10" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">City Name</label>
                            <input type="text" class="form-control" name="productname" value="<?php echo $place->cityname ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Price</label>
                            <input type="text" class="form-control mb-3" name="productprice" value="<?php echo $place->price.' '.$place->currency ?>">
                            <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button" data-key="pk_test_51OGUPwC37LiInHr2iIwVJgn4S1O80TUqRC5PmnGllhp0YS64szWxvL00eAqGxh0Vl0QjkcoHexk1pWM8lefOCKOM006wXxSVMy"
                            data-amount="<?php echo $place->price*100?>"
                            data-name="<?php echo $place->cityname  ?>"
                            data-image="<?php echo $place->imagepath ?>"
                            data-currency="<?php echo $place->currency ?>"
                            
                            ></script>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $OUTPUT->footer();
?>