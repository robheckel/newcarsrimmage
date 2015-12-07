<?php
session_start();
include_once 'class/class.pagebuild.php';
$page = new pageBuild();
$title = "New Car Scrimmage - Account";
$page->defaultHead($title);
// Start any script or style customizations
$page->startBody();
$page->navBar();
$_SESSION['userInfo'] = $_POST;

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
<h2>Create an Account</h2>
<p>Please create a username and password combination.  This will be used to access your central control panel where you can view information about your deals, read offers, and compare them against other local deals.</p>
<form class="form-horizontal" method="POST" action="utils/signup.php">
<fieldset>

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
    <input id="passwordinput" name="passwordinput" type="password" placeholder="Required" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="createbutton"></label>
  <div class="col-md-4">
    <button id="createbutton" name="createbutton" class="btn btn-success">Create</button>
  </div>
</div>

</fieldset>
</form>

<?php
$page->footer();
?>