<?php
session_start();
if(!isset($_SESSION['LoggedIn'])){
    header('Location: http://onlinelotbuilder.com/login.php');
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
// This will help deal with any sanitize warnings we may see.
foreach( $_POST as $key => $value ){
    $_POST[$key] = $db->filter( $value );
}
foreach( $_GET as $key => $value ){
    $_GET[$key] = $db->filter( $value );
}
if(isset($_SESSION['cusID'])){
    $cusID = $_SESSION['cusID'];
}
if(isset($_SESSION['dealerID'])){
    $dealerID = $_SESSION['dealerID'];
}
if(isset($_GET['req'])){
    $rID = $_GET['req'];
}


/**
 * At this point, if we have both a request ID and Customer ID,
 * time to remove the request; however, we'll also need to remove
 * any outstanding offers.  
 */
if(isset($cusID) && $rID){
    $where = array('request_id' => $rID);
    $db->delete('offers', $where);
    $db->delete('requests', $where);
    header('Location: http://onlinelotbuilder.com/controlpanel.php');
}elseif(isset($dealerID) && isset($rID)){
    $where = array('inventory_id' => $rID, 'dealer_id' => $dealerID);
    $db->delete('inventory', $where);
    header('Location: http://onlinelotbuilder.com/controlpanel.php');
}

?>