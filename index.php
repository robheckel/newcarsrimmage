<?php
session_start();
include_once 'class/class.pagebuild.php';
$page = new pageBuild();
$title = "New Car Scrimmage - Home";
$page->defaultHead($title);
// Start any script or style customizations
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
<style>
#transport:hover {
    background-color: #cccccc;
}
</style>
<?php
$page->startBody();
$page->navBar();
?>
        <h1>Welcome, Let's get started!</h1>
        <p>Below are 4 vehicle body styles.  To get started, please click on the type of vehicle that you are interested in.  Once you've made a selection, you'll be able to add features and more!</p>
        <a href="choosevehicle.php?choice=car"><img id="transport" src="images/car.png"></a>
	<a href="choosevehicle.php?choice=truck"><img id="transport" src="images/truck.png"></a>
	<a href="choosevehicle.php?choice=suv"><img id="transport" src="images/suv.png"></a>
	<a href="choosevehicle.php?choice=van"><img id="transport" src="images/van.png"></a>
<?php
$page->footer();
?>