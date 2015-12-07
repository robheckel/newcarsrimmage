<?php
session_start();
require_once '../class/class.db.php';
$config = parse_ini_file('../config/app.ini');
define( 'DB_HOST', $config['dbhost'] ); // set database host
define( 'DB_USER', $config['dbuser'] ); // set database user
define( 'DB_PASS', $config['dbpass'] ); // set database password
define( 'DB_NAME', $config['dbname'] ); // set database name
define( 'SEND_ERRORS_TO', $config['adminmail'] ); //set email notification email address
define( 'DISPLAY_DEBUG', true ); //display db errors?

$database = new DB();
$server = $_SERVER['HTTP_REFERER'];

// Clean up all of our inputs
// This will help deal with any sanitize warnings we may see.
foreach( $_POST as $key => $value ){
    $_POST[$key] = $database->filter( $value );
}

// Used throughout, set them now.  
$username = $_POST['username'];
$pass     = base64_encode($_POST['password']);
$customerLogin    = "SELECT * FROM users, customers WHERE username = '$username' AND password = '$pass' AND customers.user_id = users.user_id";
$dealerLogin    = "SELECT * FROM users, dealers WHERE username = '$username' AND password = '$pass' AND dealers.user_id = users.user_id";


// Clean out posted errors
if(strpos($server, '?')){
    $server = strstr($server, '?', true);
}
// Handles frontpage logins
if( $_POST['fp'] == 'fplogin' ){
    if( $database->num_rows($customerLogin) == 1 ){
        $results = $database->get_results( $customerLogin );
        foreach($results as $row){
            $_SESSION['name']= $row['username'];
            $_SESSION['user'] = $row['username'];
            $_SESSION['uid'] = $row['user_id'];
            $_SESSION['LoggedIn'] = 1;
            $_SESSION['cusID'] = $row['customer_id'];
        }
        header("Location: $server?mess=login" );
        die();
    }elseif($database->num_rows($dealerLogin) == 1){
        $results = $database->get_results( $dealerLogin );
        foreach($results as $row){
            $_SESSION['name']= $row['username'];
            $_SESSION['user'] = $row['username'];
            $_SESSION['uid'] = $row['user_id'];
            $_SESSION['LoggedIn'] = 1;
            $_SESSION['dealerID'] = $row['dealer_id'];
            $_SESSION['dealer'] = true;
        }
        header("Location: {$config['baseurl']}controlpanel.php" );
        die();
    }else{
        header("Location: $server?err=wrongpassword" );
        die();
    }
}

// Handles frontpage logins
if( isset( $_POST['login'] ) ){
    $username = $_POST['username'];
    $pass     = base64_encode($_POST['password']);
    if( $database->num_rows($customerLogin) == 1 ){
        $results = $database->get_results( $customerLogin );
        foreach($results as $row){
            $_SESSION['name']= $row['username'];
            $_SESSION['user'] = $row['username'];
            $_SESSION['uid'] = $row['user_id'];
            $_SESSION['LoggedIn'] = 1;
            $_SESSION['cusID'] = $row['customer_id'];
        }
        header("Location: {$config['baseurl']}controlpanel.php" );
        die();
    }elseif($database->num_rows($dealerLogin) == 1){
        $results = $database->get_results( $dealerLogin );
        foreach($results as $row){
            $_SESSION['name']= $row['username'];
            $_SESSION['user'] = $row['username'];
            $_SESSION['uid'] = $row['user_id'];
            $_SESSION['LoggedIn'] = 1;
            $_SESSION['dealerID'] = $row['dealer_id'];
            $_SESSION['dealer'] = true;
        }
        header("Location: {$config['baseurl']}controlpanel.php" );
        die();
    }else{
        header("Location: $server?err=wrongpassword" );
        die();
    }
}
?>