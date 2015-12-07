<?php
session_start();
include_once 'class/class.pagebuild.php';
$page = new pageBuild();
$title = "New Car Scrimmage - Account";
$page->defaultHead($title);
// Start any script or style customizations
$type = $_GET['choice'];

if(isset($_GET['err'])){
    if($_GET['err'] == "wrongpassword"){
        $page->dialog('Login Incorrect');
    }elseif($_GET['err'] == 'logout'){
        $page->dialog('Successfully logged out');
    }
}

if(isset($_GET['mess'])){
    if($_GET['mess'] === 'login'){
        $page->dialog('Successfully logged in');
    }
}

?>
<script>
function getState(val) {
    $.ajax({
        type: "POST",
        url: "utils/dependentCars.php",
        data: {make: val, type: "<?php echo $type;?>"},
        success: function(data){
            $("#model").html(data);
        }
    });
    $(".trim").hide();
    $(".model").removeAttr("hidden");
    $("#progressbar").css('width', '28%');
}

function getTrim(val) {
    $.ajax({
        type: "POST",
        url: "utils/dependentCars.php",
        data: {model: val, make: $("#make").val()},
        success: function(data){
            $("#trim").html(data);
        }
    });
    $(".trim").removeAttr("hidden");
    $(".trim").show();
    $("#progressbar").css('width', '35%');
}

function letGo(){
    $("#go").removeAttr("disabled");
    $("#progressbar").css('width', '50%');
}
</script>
<?php
$page->startBody();
$page->navBar();
if(isset($_SESSION['LoggedIn'])){
    $destination = "utils/addcar.php";
}else{
    $destination = "aboutyou.php";
}
?>
<h2>Choose a Vehicle</h2>
<form class="form-horizontal" method="POST" action="<?php echo $destination; ?>">
<fieldset>

<!-- Form Name -->
<legend></legend>

<div class="progress">
    <div class="progress-bar progress-bar-striped active" id="progressbar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
    <span class="sr-only">25% Complete</span>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Make</label>
  <div class="col-md-4">
    <select id="make" name="make" class="form-control" onChange="getState(this.value);">
        <option>Select a Make</option>
        <?php
        $_GET['action'] = 'modelsByType';
        $_GET['type'] = $type;
        include 'utils/dependentCars.php';
        ?>
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group model" hidden>
  <label class="col-md-4 control-label" for="model">Model</label>
  <div class="col-md-4">
    <select id="model" name="model" class="form-control" onChange="getTrim(this.value);">
        <option>Select a Model</option>
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group trim" hidden>
  <label class="col-md-4 control-label" for="trim">Trim</label>
  <div class="col-md-4">
    <select id="trim" name="trim" class="form-control" onChange="letGo();">
    </select>
  </div>
</div>
<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="go"></label>
  <div class="col-md-4">
      <button id="go" name="go" class="btn btn-warning" disabled="true">Continue</button>
  </div>
</div>

</fieldset>
</form>

<?php
$page->footer();
?>