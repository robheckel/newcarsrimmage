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

if(isset($_SESSION['LoggedIn'])){
    if(isset($_POST['update'])){
        $password = $_POST['password'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip = $_POST['zip'];
        $uid = $_POST['uid'];
        
        if(isset($_POST['cusID'])){
            //Update the customer tables
            
            $where = array('user_id' => $uid);
            if($password !== ''){
                $data = array('password'=>  base64_encode($password));
                $database->update('users', $data, $where);
            }
            $where = array('customer_id' => $_POST['cusID']);
            if($name !== ''){
                $data = array('customer_name' => $name);
                $database->update('customers', $data, $where);
            }
            if($email !== ''){
                $data = array('customer_email'=>$email);
                $database->update('customers', $data, $where);
            }
            if($phone !== ''){
                $data = array('customer_phone'=>$phone);
                $database->update('customers', $data, $where);
            }
            if($address !== ''){
                $data = array('customer_address'=>$address);
                $database->update('customers', $data, $where);
            }
            if($city !== ''){
                $data = array('customer_city'=>$city);
                $database->update('customers', $data, $where);
            }
            if($state !== ''){
                $data = array('customer_state'=>$state);
                $database->update('customers', $data, $where);
            }
            if($zip !== ''){
                $data = array('customer_zip'=>$zip);
                $database->update('customers', $data, $where);
            }
            header('Location: http://onlinelotbuilder.com/controlpanel.php');
        }elseif(isset($_POST['dealerID'])){
            //Update the dealer tables
            $where = array('user_id' => $uid);
            if($password !== ''){
                $data = array('password'=>  base64_encode($password));
                $database->update('users', $data, $where);
            }
            $where = array('dealer_id' => $_POST['dealerID']);
            if($name !== ''){
                $data = array('dealer_name' => $name);
                $database->update('dealers', $data, $where);
            }
            if($email !== ''){
                $data = array('dealer_email'=>$email);
                $database->update('dealers', $data, $where);
            }
            if($phone !== ''){
                $data = array('dealer_phone'=>$phone);
                $database->update('dealers', $data, $where);
            }
            if($address !== ''){
                $data = array('dealers_address'=>$address);
                $database->update('dealers', $data, $where);
            }
            if($city !== ''){
                $data = array('dealer_city'=>$city);
                $database->update('dealers', $data, $where);
            }
            if($state !== ''){
                $data = array('dealer_state'=>$state);
                $database->update('dealers', $data, $where);
            }
            if($zip !== ''){
                $data = array('dealer_zip'=>$zip);
                $database->update('dealers', $data, $where);
            }
            header('Location: http://onlinelotbuilder.com/controlpanel.php');
        }
    }
}
//Handle Dealer Sign up
if(isset($_POST['dealer'])){
    //print_r($_POST);
    //die;
//    Array ( [username] => donnell [password] => donnell [name] => Donnell Ford [phone] => (330) 726-8181 [address] => 7955 Market St [dealer] => true [city] => Youngstown [state] => OH [zip] => 44512 [singlebutton] => )
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