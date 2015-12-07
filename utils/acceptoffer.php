<?php
session_start();
if(!isset($_SESSION['LoggedIn'])){
    header('Location: http://onlinelotbuilder.com/login.php');
}
// Dealers shouldn't accept offers
if(isset($_SESSION['dealer'])){
    header('Location: http://onlinelotbuilder.com/controlpanel.php');
}
require_once '../class/class.db.php';
$config = parse_ini_file('../config/app.ini');
define( 'DB_HOST', $config['dbhost'] ); // set database host
define( 'DB_USER', $config['dbuser'] ); // set database user
define( 'DB_PASS', $config['dbpass'] ); // set database password
define( 'DB_NAME', $config['dbname'] ); // set database name
define( 'SEND_ERRORS_TO', $config['adminmail'] ); //set email notification email address
define( 'DISPLAY_DEBUG', true ); //display db errors?
$db = new DB();

// Clean up all of our inputs
foreach( $_GET as $key => $value ){
    $_GET[$key] = $db->filter( $value );
}


$offer = $_GET['oid'];
$cusID = $_SESSION['cusID'];

$query = "SELECT * FROM offers WHERE offer_id = $offer";

$results = $db->get_results($query);
foreach($results as $key=>$value){
    $reqID = $value['request_id'];
    $check = "SELECT * FROM requests WHERE request_id = $reqID AND customer_id = $cusID";
    if($db->num_rows($check) > 0){
        if(isset($_GET['reject'])){
            // We're going to reject this.
            $runUP = "UPDATE offers SET `offer_rejected` = 1 WHERE offer_id = $offer";
        }else{
            $runUP = "UPDATE offers SET `offer_accepted` = 1 WHERE offer_id = $offer";
        }
        $db->query($runUP);
        header('Location: http://onlinelotbuilder.com/includes/messages.php');
    }else{
        header('Location: http://onlinelotbuilder.com/includes/messages.php');
        die;
    }
}
?>s