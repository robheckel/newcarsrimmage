<?php
session_start();
include_once '../class/class.user.php';
$user = new user();
include_once '../class/class.cars.php';
$car = new cars();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$req = $_GET['req'];

if(isset($_SESSION['dealer'])){
    // I'm a dealer
    $request = $user->getRequest($req);
    $request = $request[0];
    $id = $_SESSION['dealerID'];
    $select = "SELECT * FROM offers WHERE dealer_id = $id AND request_id = $req";
    $results = $user->db->get_results($select);
    $results = $results[0];
    $trim = $car->getNameByTrim($request['car_trim']);
    $price = $amount = "$". number_format($results['offered_price'], 2, '.', ',');
    $getCustomer = "SELECT * FROM customers WHERE customer_id IN (SELECT customer_id FROM requests WHERE request_id = $req)";
    $cus = $user->db->get_results($getCustomer);
    $cus = $cus[0];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Remote file for Bootstrap Modal</title>  
</head>
<body>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Lead Information</h4>
            </div>			<!-- /modal-header -->
            <div class="modal-body">
                <p><b>Name:</b> <?php echo $cus['customer_name']; ?></p>
                <p><b>Email:</b> <?php echo $cus['customer_email']; ?></p>
                <p><b>Phone:</b> <?php echo $cus['customer_phone']; ?></p>
                <p><b>ZipCode:</b> <?php echo $cus['customer_zip']; ?></p>
                <p><b>Price:</b> <?php echo $price; ?></p>
                <p><b>Make:</b> <?php echo $request['makeName']; ?></p>
                <p><b>Model:</b> <?php echo $request['name']; ?></p>
                <p><b>Trim:</b> <?php echo $trim['long']; ?> </p>
                <p><b>Your Message:</b> <?php echo $results['offer_details']; ?>
                
            </div>			<!-- /modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>			<!-- /modal-footer -->
</body>
</html>

