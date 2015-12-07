<?php
session_start();
$_SESSION['vehicle'] = $_GET['choice'];
include_once 'class/class.pagebuild.php';
$page = new pageBuild();
$title = "New Car Scrimmage - Features";
$page->defaultHead($title);
// Start any script or style customizations
$page->startBody();
$page->navBar();
?>
<form class="form-horizontal" method="POST" action="aboutyou.php">
<fieldset>

<!-- Form Name -->
<legend>Desired Features</legend>

<!-- Multiple Checkboxes -->
<div class="form-group">
  <label class="col-md-4 control-label" for="exteriorsFeatures">Exterior Features</label>
  <div class="col-md-4">
  <div class="checkbox">
    <label for="exteriorsFeatures-0">
      <input type="checkbox" name="exteriorsFeatures[]" id="exteriorsFeatures-0" value="HID">
      HID Headlights
    </label>
	</div>
  <div class="checkbox">
    <label for="exteriorsFeatures-1">
      <input type="checkbox" name="exteriorsFeatures[]" id="exteriorsFeatures-1" value="FOG">
      Fog Lights
    </label>
	</div>
  <div class="checkbox">
    <label for="exteriorsFeatures-2">
      <input type="checkbox" name="exteriorsFeatures[]" id="exteriorsFeatures-2" value="SUNROOF">
      Sunroof/Moonroof
    </label>
	</div>
  <div class="checkbox">
    <label for="exteriorsFeatures-3">
      <input type="checkbox" name="exteriorsFeatures[]" id="exteriorsFeatures-3" value="CONVERTIBLE">
      Convertible
    </label>
	</div>
  </div>
</div>

<!-- Multiple Checkboxes -->
<div class="form-group">
  <label class="col-md-4 control-label" for="insideFeatures">Interior &amp; Electronics</label>
  <div class="col-md-4">
  <div class="checkbox">
    <label for="insideFeatures-0">
      <input type="checkbox" name="insideFeatures[]" id="insideFeatures-0" value="rcamera">
      Reverse Camera
    </label>
	</div>
  <div class="checkbox">
    <label for="insideFeatures-1">
      <input type="checkbox" name="insideFeatures[]" id="insideFeatures-1" value="keyless">
      Keyless Entry
    </label>
	</div>
  <div class="checkbox">
    <label for="insideFeatures-2">
      <input type="checkbox" name="insideFeatures[]" id="insideFeatures-2" value="leather">
      Leather Interior
    </label>
	</div>
  <div class="checkbox">
    <label for="insideFeatures-3">
      <input type="checkbox" name="insideFeatures[]" id="insideFeatures-3" value="navigation">
      Navigation
    </label>
	</div>
  <div class="checkbox">
    <label for="insideFeatures-4">
      <input type="checkbox" name="insideFeatures[]" id="insideFeatures-4" value="satellite">
      Satellite Radio
    </label>
	</div>
  </div>
</div>

<!-- Multiple Checkboxes -->
<div class="form-group">
  <label class="col-md-4 control-label" for="mechFeatures">Mechanical</label>
  <div class="col-md-4">
  <div class="checkbox">
    <label for="mechFeatures-0">
      <input type="checkbox" name="mechFeatures[]" id="mechFeatures-0" value="AWD">
      AWD/4WD
    </label>
	</div>
  <div class="checkbox">
    <label for="mechFeatures-1">
      <input type="checkbox" name="mechFeatures[]" id="mechFeatures-1" value="AUTO">
      Automatic Transmission
    </label>
	</div>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="continueButton"></label>
  <div class="col-md-4">
    <button type="submit" id="continueButton" name="continueButton" class="btn btn-warning">Continue</button>
  </div>
</div>

</fieldset>


</form>
<?php
$page->footer();
?>