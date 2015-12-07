<?php
session_start();
if(!isset($_SESSION['LoggedIn'])){
    header('Location: http://onlinelotbuilder.com/login.php');
}
header('Content-Type: text/plain');
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

$year   = $config['apiyr'];
$make   = $_POST['make'];
$model  = $_POST['model'];
$trim   = $_POST['trim'];
$uid    = $_SESSION['uid'];

if(isset($_SESSION['cusID'])){
    $cusID  = $_SESSION['cusID'];
    $addThis = array(
      'customer_id' => $cusID,
      'car_year'    => $year,
      'car_make'    => $make,
      'car_model'   => $model,
      'car_trim'    => $trim
    );

    if($db->insert('requests', $addThis)){
        header('Location: http://onlinelotbuilder.com/controlpanel.php');
    }
}elseif(isset($_SESSION['dealerID'])){
    $dID = $_SESSION['dealerID'];
    $addThis = array(
        'dealer_id'   => $dID,
        'car_year'    => $year,
        'car_make'    => $make,
        'car_model'   => $model,
        'car_trim'    => $trim
    );
    
    if($db->insert('inventory', $addThis)){
        header('Location: http://onlinelotbuilder.com/controlpanel.php');
    }
}
?>