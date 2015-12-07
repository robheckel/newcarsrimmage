<?php
session_start();
include_once 'class/class.pagebuild.php';
$page = new pageBuild();
$title = "New Car Scrimmage - Log In";
$page->defaultHead($title);
// Start any script or style customizations
$page->startBody();
$page->navBar();
if(isset($_GET['err'])){
    if($_GET['err'] == "wrongpassword"){
        $page->dialog('Login Incorrect');
    }elseif($_GET['err'] == 'logout'){
        $page->dialog('Successfully logged out');
    }
}
?>

<form class="form-horizontal" method="POST" action="utils/auth.php">
<fieldset>
    <h2>Log In</h2>
<!-- Form Name -->
<legend></legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="username">Username</label>  
  <div class="col-md-4">
  <input id="username" name="username" type="text" placeholder="Required" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordinput">Password</label>
  <div class="col-md-4">
    <input id="password" name="password" type="password" placeholder="Required" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Hide Login so we can handle this -->
<input type="hidden" name="login" value="true">

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="createbutton"></label>
  <div class="col-md-4">
    <button id="createbutton" name="createbutton" class="btn btn-success">Log In</button>
  </div>
</div>

</fieldset>
</form>

<?php
$page->footer();
?>