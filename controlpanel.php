<?php
session_start();
$config = parse_ini_file('config/app.ini');
include_once 'class/class.db.php';
define( 'DB_HOST', $config['dbhost'] ); // set database host
define( 'DB_USER', $config['dbuser'] ); // set database user
define( 'DB_PASS', $config['dbpass'] ); // set database password
define( 'DB_NAME', $config['dbname'] ); // set database name
define( 'SEND_ERRORS_TO', $config['adminmail'] ); //set email notification email address
define( 'DISPLAY_DEBUG', true ); //display db errors?

$database = new DB();

include_once 'class/class.pagebuild.php';
include_once 'class/class.user.php';
include_once 'class/class.cars.php';

$user = new user();
$page = new pageBuild();
$cars = new cars();
$page->checkLogin();
$title = "New Car Scrimmage - Control Panel";
$page->defaultHead($title);
// Start any script or style customizations


//Beginning of mycars/inventory table
$carTable = <<<STARTTABLE
<table class="table table-hover">
                <thead>
                  <tr>
                    <th>Year</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Trim</th>
                    <th>Remove</th>
                  </tr>
                </thead>
                <tbody>
STARTTABLE;

/**
 * The following will determine specifics depending on whether the user is a
 * buyer or a dealer.  This includes a number of different search results and
 * variables that will be reused throughout this page.
 */

if(isset($_SESSION['dealer'])){
    $id = $_SESSION['dealerID'];
    // BUILD OUT CHARTS
    $offerCounts = "SELECT COUNT(offer_id) AS allOffers, COUNT(offer_rejected) AS rejects, COUNT(offer_accepted) AS accepts "
            . "FROM offers "
            . "WHERE dealer_id = $id";
    $offerResults = $database->get_results($offerCounts);
    //Array ( [allOffers] => 3 [rejects] => 1 [accepts] => 2 ) )
    foreach($offerResults as $key=>$value){
        $all = $value['allOffers'];
        $rejects = $value['rejects'];
        $accepts = $value['accepts'];
        $pending = $all - $rejects - $accepts;
        
        /**
         * Convert the above values into something like this: 
         * ['Accepted',     11],
         * ['Rejected',      2],
         * ['Pending',  2]
         */
        
        $pieVal = "";
        if($pending > 0){
            $pieVal .= "['Pending', $pending],";
        }
        if($accepts > 0){
            $pieVal .= "['Accepted', $accepts],";
        }
        if($rejects > 0){
            $pieVal .= "['Rejected', $rejects],";
        }
        $pieVal = rtrim($pieVal, ',');
    }
    
    // END CHARTS
    
    
    $query    = "SELECT * FROM users, dealers WHERE dealer_id = $id AND dealers.user_id = users.user_id";
    $results = $database->get_results($query);
    foreach($results as $key=>$value){
        $name = $value['dealer_name'];
        $email = $value['dealer_email'];
        $phone = $value['dealer_phone'];
        $address = $value['dealer_address'];
        $city = $value['dealer_city'];
        $zip = $value['dealer_zip'];
        $state = $value['dealer_state'];
    }

    // Since we're a dealer, we need to get a list of our inventory
    // This will populate the 'My Cars' Section
    $myCars = $user->getInventory($id);
    $check = array();

    foreach($myCars as $key=>$value){
        $invID = $value['inventory_id'];
        $trim = $value['car_trim'];
        $year = $value['car_year'];
        $make = $value['makeName'];
        $model = $value['name'];

        //We'll use this later...
        array_push($check, $trim);

        $more = $cars->getNameByTrim($trim);
        $longName = $more['long'];

        $carTable .= <<<CARINFO
<tr>
    <td>$year</td>
    <td>$make</td>
    <td>$model</td>
    <td>$longName</td>
    <td style='text-align: center;'><a href="utils/delcar.php?req=$invID"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
CARINFO;

    }

    // Lets find some matches and build out that INFO
        $matchTable = <<<MATCHTABLE
<table class="table table-hover">
                <thead>
                  <tr>
                    <th>Year</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Trim</th>
                    <th>Distance</th>
                    <th>Send Offer</th>
                  </tr>
                </thead>
                <tbody>
MATCHTABLE;

    foreach($check as $trimKey){

        $getMatches = "SELECT request_id, requests.customer_id, customer_zip, car_year, car_make, car_model, car_trim, makeName, name "
               . "FROM requests, make, model, customers "
               . "WHERE car_make = makeID AND car_model = modelID AND car_trim = $trimKey AND requests.customer_id = customers.customer_id";
        $matches = $database->get_results($getMatches);
        foreach($matches as $key=>$value){
            $reqid = $value['request_id'];
            $qCheck = "SELECT * FROM offers WHERE request_id = $reqid AND dealer_id = $id";
            if($database->num_rows($qCheck) > 0){
                $offer = "<td style='text-align: center;'><span class='glyphicon glyphicon-ok-circle'></span></td>";
            }else{
                $offer = "<td style='text-align: center;'><a href='sendoffer.php?dealer=$id&r=$reqid'><span class='glyphicon glyphicon-share-alt'></span></a></td>";
            }
            
            $yr = $value['car_year'];
            $make = $value['makeName'];
            $model = $value['name'];
            $more = $cars->getNameByTrim($value['car_trim']);
            $longName = $more['long'];
            $cusZip = $value['customer_zip'];
            $zipURL = $config['zipurl'].$config['zipkey'].'/distance.json/'.$zip.'/'.$value['customer_zip'].'/mile';
            $getDistance = json_decode(file_get_contents($zipURL), true);
            $dis = round($getDistance['distance']);
            $matchTable .= <<<ROW
<tr>
    <td>$yr</td>
    <td>$make</td>
    <td>$model</td>
    <td>$longName</td>
    <td>$dis</td>
    $offer
</tr>
ROW;
        }
    }

    $matchTable .= <<<ENDMATCH
</tbody>
</table>
ENDMATCH;

}else{
    $id = $_SESSION['cusID'];
    $offerCounts = "SELECT COUNT(offer_id) AS allOffers, COUNT(offer_rejected) AS rejects, COUNT(offer_accepted) AS accepts "
            . "FROM offers "
            . "WHERE request_id IN (SELECT request_id FROM requests WHERE customer_id = $id)";
    $offerResults = $database->get_results($offerCounts);
    //Array ( [allOffers] => 3 [rejects] => 1 [accepts] => 2 ) )
    foreach($offerResults as $key=>$value){
        $all = $value['allOffers'];
        $rejects = $value['rejects'];
        $accepts = $value['accepts'];
        $pending = $all - $rejects - $accepts;
        
        /**
         * Convert the above values into something like this: 
         * ['Accepted',     11],
         * ['Rejected',      2],
         * ['Pending',  2]
         */
        
        $pieVal = "";
        if($pending > 0){
            $pieVal .= "['Pending', $pending],";
        }
        if($accepts > 0){
            $pieVal .= "['Accepted', $accepts],";
        }
        if($rejects > 0){
            $pieVal .= "['Rejected', $rejects],";
        }
        $pieVal = rtrim($pieVal, ',');
    }
    $query = "SELECT * FROM users, customers WHERE customer_id = $id AND customers.user_id = users.user_id";
    $results = $database->get_results($query);
    foreach($results as $key=>$value){
        $name = $value['customer_name'];
        $email = $value['customer_email'];
        $phone = $value['customer_phone'];
        $address = $value['customer_address'];
        $city = $value['customer_city'];
        $zip = $value['customer_zip'];
        $state = $value['customer_state'];
    }

    // Since we're a customer, we need to get a list of our requests
    // This will populate the 'My Cars' Section
    $myCars = $user->getMyCars($id);

    foreach($myCars as $key=>$value){
        $reqID = $value['request_id'];
        $trim = $value['car_trim'];
        $year = $value['car_year'];
        $make = $value['makeName'];
        $model = $value['name'];

        $more = $cars->getNameByTrim($trim);
        $longName = $more['long'];

        $carTable .= <<<CARINFO
<tr>
    <td>$year</td>
    <td>$make</td>
    <td>$model</td>
    <td>$longName</td>
    <td style='text-align: center;'><a href="utils/delcar.php?req=$reqID"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>
CARINFO;

    }

}
//End cartable
$carTable .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;



?>
<script>
$(document).ready(function() {
    $("#state").val("<?php echo $state; ?>");
    $(".btn-pref .btn").click(function () {
        $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
        // $(".tab").addClass("active"); // instead of this do the below
        $(this).removeClass("btn-default").addClass("btn-primary");
    });
});
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {

    var data = google.visualization.arrayToDataTable([
      ['Status', 'Offers'],
      <?php echo $pieVal; ?>
    ]);

    var options = {
        legend: 'none',
        pieSliceText: 'label',
        title: 'My Offer Summary',
        is3D: true,
        backgroundColor: { fill:'transparent' },
        colors: [ 'orange', 'green', 'red' ],
        chartArea: {
            width: '200%',
            height: '200%'
        }
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

    chart.draw(data, options);
  }
</script>

<style>
/* USER PROFILE PAGE */
 .card {
    margin-top: 20px;
    padding: 30px;
    background-color: rgba(214, 224, 226, 0.2);
    -webkit-border-top-left-radius:5px;
    -moz-border-top-left-radius:5px;
    border-top-left-radius:5px;
    -webkit-border-top-right-radius:5px;
    -moz-border-top-right-radius:5px;
    border-top-right-radius:5px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.card.hovercard {
    position: relative;
    padding-top: 0;
    overflow: hidden;
    text-align: center;
    background-color: #fff;
    background-color: rgba(255, 255, 255, 1);
}
.card.hovercard .card-background {
    height: 130px;
}
.card-background img {
    -webkit-filter: blur(25px);
    -moz-filter: blur(25px);
    -o-filter: blur(25px);
    -ms-filter: blur(25px);
    filter: blur(25px);
    margin-left: -100px;
    margin-top: -200px;
    min-width: 130%;
}
.card.hovercard .useravatar {
    position: absolute;
    top: 15px;
    left: 0;
    right: 0;
}
.card.hovercard .useravatar img {
    width: 100px;
    height: 100px;
    max-width: 100px;
    max-height: 100px;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    border: 5px solid rgba(255, 255, 255, 0.5);
}
.card.hovercard .card-info {
    position: absolute;
    bottom: 14px;
    left: 0;
    right: 0;
}
.card.hovercard .card-info .card-title {
    padding:0 5px;
    font-size: 20px;
    line-height: 1;
    color: #262626;
    background-color: rgba(255, 255, 255, 0.1);
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
.card.hovercard .card-info {
    overflow: hidden;
    font-size: 12px;
    line-height: 20px;
    color: #737373;
    text-overflow: ellipsis;
}
.card.hovercard .bottom {
    padding: 0 20px;
    margin-bottom: 17px;
}
.btn-pref .btn {
    -webkit-border-radius:0 !important;
}

.my-stats{
  text-align: center;
}

.well{
    min-height: 425px !important;
}

#open {
  height: 400px;
  width: 400px;
  background: url('images/open.png') no-repeat center center;
  background-size: contain;
}

#accepted {
  height: 400px;
  width: 400px;
  background: url('images/accepted.png') no-repeat center center;
  background-size: contain;
}

#dealerships {
  height: 400px;
  width: 400px;
  background: url('images/dealerships.png') no-repeat center center;
  background-size: contain;
}

</style>
<?php
$page->startBody();
$page->navBar();
if(isset($_SESSION['dealer'])){
    $cars = 'Inventory';
    $offers = 'Potential Buyers';
}else{
    $cars = 'My Cars';
    $offers = 'Compare Results';
}

?>


<div class="">
    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
        <div class="btn-group" role="group">
            <button type="button" id="stars" class="btn btn-primary" href="#tab1" data-toggle="tab"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
                <div class="hidden-xs">Statistics</div>
            </button>
        </div>
        <div class="btn-group" role="group">
            <button type="button" id="following" class="btn btn-default" href="#tab2" data-toggle="tab"><span class="glyphicon glyphicon-road" aria-hidden="true"></span>
                <div class="hidden-xs"><?php echo $cars; ?></div>
            </button>
        </div>
        <?php
        if(isset($_SESSION['dealer'])){ ?>
        <div class="btn-group" role="group">
            <button type="button" id="favorites" class="btn btn-default" href="#tab3" data-toggle="tab"><span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                <div class="hidden-xs"><?php echo $offers; ?></div>
            </button>
        </div>
        <?php } ?>
        <div class="btn-group" role="group">
            <button type="button" id="following" class="btn btn-default" href="#tab4" data-toggle="tab"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                <div class="hidden-xs">Inbox</div>
            </button>
        </div>
        <div class="btn-group" role="group">
            <button type="button" id="following" class="btn btn-default" href="#tab5" data-toggle="tab"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                <div class="hidden-xs">Preferences</div>
            </button>
        </div>
    </div>

    <div class="well" min-height='350px'>
      <div class="tab-content">
        <div class="tab-pane fade in active" id="tab1"> 
          <h3 class="my-stats">Statistics</h3>
          <div class='col-md-4'>
          <h3 class="my-stats">Offers</h3>
          <div id="piechart" style="width: 100%; height: 250px;"></div>
          </div>
          <div class='col-md-4'>
          <!--<div id="piechart" style="width: 100%; height: 250px;"></div>-->
          </div>
          <div class='col-md-4'>
          <!--<div id="piechart" style="width: 100%; height: 250px;"></div>-->
          </div>
          <!--
          <div>
            <h3>Open vs Closed Offers</h3>
            <div id="open">
            </div>
            <h3>Accepted vs Rejected Offers</h3>
            <div id="accepted">
            </div>
            <h3>Breakdown of My Dealerships<h3>
            <div id="dealerships">
            </div>
          </div>
          -->
        </div>
        <div class="tab-pane fade in" id="tab2">
          <h3><?php echo $cars; ?></h3>
<?php
if(isset($_SESSION['dealer'])){
    echo "<a href='newcar.php'><button class='btn btn-sm btn-success'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;&nbsp;Add a Unit</button></a>";
}else{
    echo "<a href='newcar.php'><button class='btn btn-sm btn-success'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;&nbsp;Find Another</button></a>";
}
?>
              <?php echo $carTable; ?>

        </div>
        <div class="tab-pane fade in" id="tab3">
          <h3><?php echo $offers; ?></h3>
          <p><?php echo $matchTable; ?></p>
        </div>
        <div class="tab-pane fade in" id="tab4">
          <h3>Offer Central</h3>
          <iframe src="http://onlinelotbuilder.com/includes/messages.php" width='100%' height='600px' frameBorder='0' seamless="seamless" ></iframe>
        </div>
        <div class="tab-pane fade in" id="tab5">
          <h3>Preferences</h3>
          <p>

<!-- Form Name -->
<legend></legend>

<form class="form-horizontal" action="utils/userinfo.php" method="POST">
<fieldset>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="password">Password</label>
  <div class="col-md-4">
    <input id="password" name="password" type="password" placeholder="**********" class="form-control input-md">

  </div>
</div>
<?php
    if(isset($_SESSION['dealer'])){
        $sayThis = 'Dealership Name';
    }else{
        $sayThis = 'Name';
    }
?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput"><?php echo $sayThis; ?></label>
  <div class="col-md-4">
  <input id="name" name="name" type="text" placeholder="<?php echo $name; ?>" class="form-control input-md" >

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="phone">Email</label>
  <div class="col-md-4">
  <input id="email" name="email" type="text" placeholder="<?php echo $email; ?>" class="form-control input-md" >

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="phone">Phone</label>
  <div class="col-md-4">
  <input id="phone" name="phone" type="text" placeholder="<?php echo $phone; ?>" class="form-control input-md">

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="address">Address</label>
  <div class="col-md-4">
  <input id="address" name="address" type="text" placeholder="<?php echo $address; ?>" class="form-control input-md">

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="city">City</label>
  <div class="col-md-4">
  <input id="city" name="city" type="text" placeholder="<?php echo $city; ?>" class="form-control input-md">

  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="state">State</label>
  <div class="col-md-4 state" id="myState">
    <select id="state" name="state" class="form-control">
      <option value="AK">AK</option>
      <option value="AL">AL</option>
      <option value="AZ">AZ</option>
      <option value="AR">AR</option>
      <option value="CA">CA</option>
      <option value="CO">CO</option>
      <option value="CT">CT</option>
      <option value="DE">DE</option>
      <option value="FL">FL</option>
      <option value="GA">GA</option>
      <option value="HI">HI</option>
      <option value="ID">ID</option>
      <option value="IL">IL</option>
      <option value="IN">IN</option>
      <option value="IA">IA</option>
      <option value="KS">KS</option>
      <option value="KY">KY</option>
      <option value="LA">LA</option>
      <option value="ME">ME</option>
      <option value="MD">MD</option>
      <option value="MA">MA</option>
      <option value="MI">MI</option>
      <option value="MN">MN</option>
      <option value="MS">MS</option>
      <option value="MO">MO</option>
      <option value="MT">MT</option>
      <option value="NE">NE</option>
      <option value="NV">NV</option>
      <option value="NH">NH</option>
      <option value="NJ">NJ</option>
      <option value="NM">NM</option>
      <option value="NY">NY</option>
      <option value="NC">NC</option>
      <option value="ND">ND</option>
      <option value="OH">OH</option>
      <option value="OK">OK</option>
      <option value="OR">OR</option>
      <option value="PA">PA</option>
      <option value="RI">RI</option>
      <option value="SC">SC</option>
      <option value="SD">SD</option>
      <option value="TN">TN</option>
      <option value="TX">TX</option>
      <option value="UT">UT</option>
      <option value="VT">VT</option>
      <option value="VA">VA</option>
      <option value="WA">WA</option>
      <option value="WV">WV</option>
      <option value="WI">WI</option>
      <option value="WY">WY</option>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="zip">Zip Code</label>
  <div class="col-md-4">
  <input id="zip" name="zip" type="text" placeholder="<?php echo $zip; ?>" class="form-control input-md">
  </div>
</div>

<?php
if(isset($_SESSION['dealerID'])){
    echo "<input type='hidden' id='dealerID' name='dealerID' value='{$_SESSION['dealerID']}'>";
}elseif(isset($_SESSION['cusID'])){
    echo "<input type='hidden' id='cusID' name='cusID' value='{$_SESSION['cusID']}'>";
}
echo "<input type='hidden' id='uid' name='uid' value='{$_SESSION['uid']}'>";
?>
<input type="hidden" id="update" name="update" value="true">
<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-success">Update</button>
  </div>
</div>

</fieldset>
</form>

          </p>
        </div>
      </div>
    </div>
    </div>
<?php
$page->footer();
?>
