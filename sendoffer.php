<?php
session_start();
include_once 'class/class.pagebuild.php';
include_once 'class/class.user.php';
include_once 'class/class.cars.php';

$car = new cars();
$user = new user();
$page = new pageBuild();
$page->checkLogin();
$title = "New Car Scrimmage - Send Offer";
$page->defaultHead($title);
$page->startBody();
$page->navBar();

//Catch anything posted to ourselves... no use for another utility.
if(isset($_POST['request'])){
    foreach( $_POST as $key => $value ){
        $_POST[$key] = $user->db->filter( $value );
    }
    $price = $_POST['price'];
    $request = $_POST['request'];
    $message = $_POST['message'];
    $dealer = $_SESSION['dealerID'];
    $input = array(
      'request_id' => $request,
      'offered_price' => $price,
      'offer_details' => $message,
      'dealer_id' => $dealer
    );
    if($user->db->insert('offers', $input)){
        header('Location: http://onlinelotbuilder.com/controlpanel.php');
    }
}

if(isset($_SESSION['dealer'])){
    foreach( $_GET as $key => $value ){
        $_GET[$key] = $user->db->filter( $value );
    }
    $rid = $_GET['r'];
    $id = $_GET['dealer'];
    $offerDeets = $user->getRequest($rid);
    $offerDeets = $offerDeets[0];
    $trimNames = $car->getNameByTrim($offerDeets['car_trim']);
    $hidden = "<input type='hidden' id='request' name='request' value='$rid'>";
    $vehicle = <<<CAR
    <tr>
        <td>{$offerDeets['car_year']}</td>
        <td>{$offerDeets['makeName']}</td>
        <td>{$offerDeets['name']}</td>
        <td>{$trimNames['long']}</td>
    </tr>
CAR;
    ?>
    <h2>Send An Offer</h2>
    <table class="table table-hover">
        <thead>
          <tr>
            <th>Year</th>
            <th>Make</th>
            <th>Model</th>
            <th>Trim</th>
          </tr>
        </thead>
        <tbody>    
        <?php echo $vehicle; ?>
        </tbody>
    </table>

    <form class="form-horizontal" method="POST" action="">
    <fieldset>
    <legend></legend>


    <div class="form-group">
      <label class="col-md-4 control-label" for="price">Price</label>  
      <div class="col-md-4">
      <input id="price" name="price" type="number" step="0.01" placeholder="$xx,xxx" class="form-control input-md">

      </div>
    </div>

    <!-- Textarea -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="message">Text Area</label>
      <div class="col-md-4">                     
        <textarea class="form-control" id="message" name="message"></textarea>
      </div>
    </div>
     <?php echo $hidden; ?>
    <!-- Button -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="go"></label>
      <div class="col-md-4">
          <button id="go" name="go" class="btn btn-success" >Send Offer</button>
      </div>
    </div>

    </fieldset>
    </form>
<?php
}
$page->footer();
?>