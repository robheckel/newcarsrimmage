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

// Clean up all of our inputs
// This will help deal with any sanitize warnings we may see.
foreach( $_POST as $key => $value ){
    $_POST[$key] = $database->filter( $value );
}

//Handle Dealer Sign up
if(isset($_POST['dealer'])){
    $name = $database->filter($_POST['name']);
    $email = $database->filter($_POST['email']);
    $phone = $database->filter($_POST['phone']);
    $address = $database->filter($_POST['address']);
    $city = $database->filter($_POST['city']);
    $state = $database->filter($_POST['state']);
    $zipcode = $database->filter($_POST['zip']);
    $username = $database->filter($_POST['username']);
    $password = base64_encode($_POST['password']);
    
    $uExists  = "SELECT * FROM users WHERE username = '$username'";
    $deExists = "SELECT * FROM dealers WHERE dealer_email = '$email'";
    
    if( $database->num_rows( $uExists ) > 0 ){
        die('USERNAME EXISTS');
    } elseif( $database->num_rows( $deExists ) > 0 ) {
        die('EMAIL IS IN USE - Dealer');
    }else{
        //The fields and values to insert
        $newAccount = array(
            'username' => $username,
            'password' => $password
        );
        $database->insert( 'users', $newAccount );
        $last_id = $database->lastid();
        
        $newDealer = array(
            'user_id' => $last_id,
            'dealer_name' => $name,
            'dealer_address' => $address,
            'dealer_city' => $city,
            'dealer_state' => $state,
            'dealer_zip' => $zipcode,
            'dealer_email' => $email,
            'dealer_phone' => $phone
        );
        
        $database->insert('dealers', $newDealer);
        $dealerID = $database->lastid();
        
        // Log this in the session
        $_SESSION['Username'] = $username;
        $_SESSION['uid'] = $last_id;
        $_SESSION['EmailAddress'] = $email;
        $_SESSION['LoggedIn'] = 1;
        $_SESSION['dealerID'] = $dealerID;
        $_SESSION['name'] = $name;
        $_SESSION['dealer'] = true;
        
        //Send to control panel
        header('Location: http://onlinelotbuilder.com/controlpanel.php');
    }
}

//If they're already logged in, they shouldn't be here.  
if(!isset($_SESSION['LoggedIn'])){
    
    // Clean out posted errors
    if(strpos($_SERVER['HTTP_REFERER'], '?')){
        $server = strstr($_SERVER['HTTP_REFERER'], '?', true);
    }

    // Map incoming values
    $name = $database->filter($_SESSION['userInfo']['name']);
    $email = $database->filter($_SESSION['userInfo']['email']);
    $address = $database->filter($_SESSION['userInfo']['address']);
    $city = $database->filter($_SESSION['userInfo']['city']);
    $state = $database->filter($_SESSION['userInfo']['state']);
    $zipcode = $database->filter($_SESSION['userInfo']['zip']);

    $username = $_POST['username'];
    $password = base64_encode($_POST['passwordinput']);

    $uExists  = "SELECT * FROM users WHERE username = '$username'";
    $deExists = "SELECT * FROM dealers WHERE dealer_email = '$email'";
    $cuExists = "SELECT * FROM customers WHERE customer_email = '$email'";
    if( $database->num_rows( $uExists ) > 0 ){
        die('USERNAME EXISTS');
    } elseif( $database->num_rows( $deExists ) > 0 ) {
        die('EMAIL IS IN USE - Dealer');
    } elseif( $database->num_rows( $cuExists ) > 0 ) {
        die('EMAIL IS IN USE - Customer');
    } else {
        //The fields and values to insert
        $newAccount = array(
            'username' => $username,
            'password' => $password
        );
        $add_query = $database->insert( 'users', $newAccount );
        $last_id = $database->lastid();
        $newCustomer = array(
            'user_id' => $last_id,
            'customer_name' => $name,
            'customer_address' => $address,
            'customer_city' => $city,
            'customer_state' => $state,
            'customer_zip' => $zipcode,
            'customer_email' => $email
        );
        
        $addCustomer = $database->insert('customers', $newCustomer);
        $newCustomer = $database->lastid();

        // Log this in the session
        $_SESSION['Username'] = $username;
        $_SESSION['uid'] = $last_id;
        $_SESSION['EmailAddress'] = $email;
        $_SESSION['LoggedIn'] = 1;
        $_SESSION['cusID'] = $newCustomer;
        $_SESSION['name'] = $_SESSION['userInfo']['name'];
        
        $addThis = array(
            'customer_id' => $_SESSION['cusID'],
            'car_year'    => $config['apiyr'],
            'car_make'    => $_SESSION['features']['make'],
            'car_model'   => $_SESSION['features']['model'],
            'car_trim'    => $_SESSION['features']['trim']
        );

        if($database->insert('requests', $addThis)){
            header('Location: http://onlinelotbuilder.com/controlpanel.php');
        }
    }
}
?>