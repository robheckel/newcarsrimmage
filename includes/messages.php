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
if(isset($_SESSION['dealer'])){
    // I'm a dealer
    $id = $_SESSION['dealerID'];
    $select = "SELECT * FROM offers WHERE dealer_id = $id";
    $results = $user->db->get_results($select);
    $pending = array();
    $accepted = array();
    $rejected = array();
    foreach($results as $key=>$value){
        
        $price = $value['offered_price'];
        $details = $value['offer_details'];
        $request = $user->getRequest($value['request_id']);
        $request = $request[0];
        
        $request['price'] = $price;
        $request['details'] = $details;
        $trims = $car->getNameByTrim($request['car_trim']);
        $request['trim'] = $trims['long'];
        if(!is_null($value['offer_accepted'])){
            $accepted[] = $request;
        }elseif(!is_null($value['offer_rejected'])){
            $rejected[] = $request;
        }else{
            $pending[] = $request;
        }
    }

    // All pending offers
    if(count($pending) > 0){
        $pendingOffers = <<<STARTTABLE
<table class="table table-hover">
    <thead>
      <tr>
        <th>Year</th>
        <th>Make</th>
        <th>Model</th>
        <th>Trim</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>     
STARTTABLE;
        
        foreach($pending as $k=>$v){
            $amount = "$". number_format($v['price'], 2, '.', ',');
            $pendingOffers .= <<<MIDSECTION
            <tr>
                <td>{$v['car_year']}</td>
                <td>{$v['makeName']}</td>
                <td>{$v['name']}</td>
                <td>{$v['trim']}</td>
                <td>$amount</td>
            </tr>
MIDSECTION;
        }
        
        $pendingOffers .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;
    }else{
        $pendingOffers = "<h3>No offers pending currently.</h3>";
    }
    
 // All accepted offers
    if(count($accepted) > 0){
        $acceptedOffers = <<<STARTTABLE
<table class="table table-hover">
    <thead>
      <tr>
        <th>Year</th>
        <th>Make</th>
        <th>Model</th>
        <th>Trim</th>
        <th>Price</th>
        <th></th>
      </tr>
    </thead>
    <tbody>     
STARTTABLE;
        
        foreach($accepted as $k=>$v){
            $amount = "$". number_format($v['price'], 2, '.', ',');
            $req = $v['request_id'];
            $acceptedOffers .= <<<MIDSECTION
            <tr style="background-color: #99FF99;">
                <td>{$v['car_year']}</td>
                <td>{$v['makeName']}</td>
                <td>{$v['name']}</td>
                <td>{$v['trim']}</td>
                <td>$amount</td>
                <td><a data-toggle="modal" data-id="$req" class="btn btn-info check" data-target="#myModal"><span class='glyphicon glyphicon-user'></span></a></td>
   </tr>
MIDSECTION;
        }
        
        $acceptedOffers .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;
    }else{
        $acceptedOffers = "<h3>No offers accepted at this time.</h3>";
    }
    
    // Rejected for dealers
    if(count($rejected) > 0){
        $rejectedOffers = <<<STARTTABLE
<table class="table table-hover">
    <thead>
      <tr>
        <th>Year</th>
        <th>Make</th>
        <th>Model</th>
        <th>Trim</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>     
STARTTABLE;
        
        foreach($rejected as $k=>$v){
            $amount = "$". number_format($v['price'], 2, '.', ',');
            $rejectedOffers .= <<<MIDSECTION
            <tr style="background-color: #ffccb3;">
                <td>{$v['car_year']}</td>
                <td>{$v['makeName']}</td>
                <td>{$v['name']}</td>
                <td>{$v['trim']}</td>
                <td>$amount</td>
            </tr>
MIDSECTION;
        }
        
        $rejectedOffers .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;
    }else{
        $rejectedOffers = "<h3>No offers rejected currently.</h3>";
    }
    
}else{
    // I'm a buyer
    $id = $_SESSION['cusID'];
    
    $select = "SELECT *" .
              "FROM offers ".
              "WHERE request_id IN (SELECT request_id FROM requests WHERE customer_id = $id) ".
              "ORDER BY offered_price ASC";
    $results = $user->db->get_results($select);
    $pending = array();
    $accepted = array();
    foreach($results as $key=>$value){

        $price = $value['offered_price'];
        $details = $value['offer_details'];
        $request = $user->getRequest($value['request_id']);
        $request = $request[0];

        $request['offer_id'] = $value['offer_id'];
        $request['price'] = $price;
        $request['details'] = $details;
        $trims = $car->getNameByTrim($request['car_trim']);
        $request['trim'] = $trims['long'];
        if(!is_null($value['offer_accepted'])){
            $accepted[] = $request;
        }elseif(!is_null($value['offer_rejected'])){
            $rejected[] = $request;
        }else{
            $pending[] = $request;
        }
    }

    // All pending offers
    if(count($pending) > 0){
        $pendingOffers = <<<STARTTABLE
<table class="table table-hover">
    <thead>
      <tr>
        <th>Year</th>
        <th>Make</th>
        <th>Model</th>
        <th>Trim</th>
        <th>Message</th>
        <th>Price</th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>     
STARTTABLE;
        
        foreach($pending as $k=>$v){
            $amount = "$". number_format($v['price'], 2, '.', ',');
            $pendingOffers .= <<<MIDSECTION
            <tr>
                <td>{$v['car_year']}</td>
                <td>{$v['makeName']}</td>
                <td>{$v['name']}</td>
                <td>{$v['trim']}</td>
                <td>{$v['details']}</td>
                <td>$amount</td>
                <td><a href="http://onlinelotbuilder.com/utils/acceptoffer.php?oid={$v['offer_id']}"><button class="btn btn-sm btn-success">Accept</button></a></td>
                <td><a href="http://onlinelotbuilder.com/utils/acceptoffer.php?oid={$v['offer_id']}&reject=1"><button class="btn btn-sm btn-danger">Reject</button></a></td>
   </tr>
MIDSECTION;
        }
        
        $pendingOffers .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;
    }else{
        $pendingOffers = "<h3>No offers pending currently.</h3>";
    }
    
    // All accepted offers
    if(count($accepted) > 0){
        $acceptedOffers = <<<STARTTABLE
<table class="table table-hover">
    <thead>
      <tr>
        <th>Year</th>
        <th>Make</th>
        <th>Model</th>
        <th>Trim</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>     
STARTTABLE;
        
        foreach($accepted as $k=>$v){
            $amount = "$". number_format($v['price'], 2, '.', ',');
            $acceptedOffers .= <<<MIDSECTION
            <tr style="background-color: #99FF99;">
                <td>{$v['car_year']}</td>
                <td>{$v['makeName']}</td>
                <td>{$v['name']}</td>
                <td>{$v['trim']}</td>
                <td>$amount</td>
            </tr>
MIDSECTION;
        }
        
        $acceptedOffers .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;
    }else{
        $acceptedOffers = "<h3>No offers accepted at this time.</h3>";
    }
    
    // Rejected for dealers
    if(count($rejected) > 0){
        $rejectedOffers = <<<STARTTABLE
<table class="table table-hover">
    <thead>
      <tr>
        <th>Year</th>
        <th>Make</th>
        <th>Model</th>
        <th>Trim</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>     
STARTTABLE;
        
        foreach($rejected as $k=>$v){
            $amount = "$". number_format($v['price'], 2, '.', ',');
            $rejectedOffers .= <<<MIDSECTION
            <tr style="background-color: #ffccb3;">
                <td>{$v['car_year']}</td>
                <td>{$v['makeName']}</td>
                <td>{$v['name']}</td>
                <td>{$v['trim']}</td>
                <td>$amount</td>
            </tr>
MIDSECTION;
        }
        
        $rejectedOffers .= <<<ENDTABLE
</tbody>
</table>
ENDTABLE;
    }else{
        $rejectedOffers = "<h3>No offers rejected currently.</h3>";
    }
    
    
}
?>
<html>
    <head>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
-->
<script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/js/bootstrap.min.js"></script>
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->           

        <script>
     jQuery(function($){
         $('a.check').click(function(ev){
             ev.preventDefault();
             var uid = $(this).data('id');
             $.get('http://onlinelotbuilder.com/includes/buyerinfo.php?req=' + uid, function(html){
                 $('.modal-content').html(html);
                 $('.modal').addClass('in');
             });
         });
    });       
        </script>
        
<style>
body {
    background-color: #f5f5f5;
}
.panel-heading .accordion-toggle:after {
    /* symbol for "opening" panels */
    font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
    content: "\e114";    /* adjust as needed, taken from bootstrap.css */
    float: right;        /* adjust as needed */
    color: grey;         /* adjust as needed */
}
.panel-heading .accordion-toggle.collapsed:after {
    /* symbol for "collapsed" panels */
    content: "\e080";    /* adjust as needed, taken from bootstrap.css */
}    
</style>            
    </head>
    <body>
        
<?php
//Flip Flop things a bit...
if(isset($_SESSION['cusID'])){
    $title1 = 'Pending Offers';
    $t1content = $pendingOffers;
    $title2 = 'Accepted Offers';
    $t2content = $acceptedOffers;
}else{
    $title1 = 'Accepted Offers'; 
    $t1content = $acceptedOffers;
    $title2 = 'Pending Offers';
    $t2content = $pendingOffers;
}
?>
<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
          <?php echo $title1; ?>
      </a>
    </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
        <?php echo $t1content; ?>
        </div>
    </div>
  </div>
  <div class="panel panel-default">
      <div class="panel-heading">
          <h4 class="panel-title">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
          <?php echo $title2; ?>
            </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
<?php echo $t2content; ?>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
   <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
          Rejected Offers
        </a>
    </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
<?php echo $rejectedOffers; ?>
      </div>
    </div>
  </div>
</div>
        
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

    </body>
</html>
