<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author rob
 */
class pageBuild {

    public function defaultHead($title = "New Car Scrimmage"){
        $head = <<<HEAD
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>$title</title>
        <!-- Latest Stable jQuery -->
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->           
HEAD;
        echo $head;
        
    }
    
    public function startBody(){
        $bodyStart = <<<BODY
     </head>
     <body>
BODY;
        echo $bodyStart;
    }
    
    public function checkLogin(){
        if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] == 1){
            
        }else{
            header('Location: http://onlinelotbuilder.com/login.php');
        }
    }
    
    public function navBar(){
        
        if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] > 0) {
            $topright = <<<TOPRIGHT
        <div id="navbar" class="navbar-collapse collapse">
          <!-- <form class="navbar-form navbar-right" method="POST" action="controlpanel.php"> -->
            <div class="navbar-form navbar-right">
            <div class="form-group">
              <p style="color: #9d9d9d;">Welcome {$_SESSION['name']}!</p>
            </div>
            <a href="controlpanel.php"><button type="submit"  class="btn btn-sm btn-success">Control Panel</button></a>
            <a href="logout.php"><button type="submit" class="btn btn-sm btn-danger">Logout</button></a>
          <!--</form> -->
              </div>
        </div><!--/.navbar-collapse -->
TOPRIGHT;
        }else{
            $topright = <<<TOPRIGHT
        <div id="navbar" class="navbar-collapse collapse">
          <form class="navbar-form navbar-right" method="POST" action="utils/auth.php">
            <div class="form-group">
              <input type="text" id="username" name="username" placeholder="Username" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" id="password" name="password" placeholder="Password" class="form-control">
            </div>
	    <input type="hidden" value="fplogin" name="fp" />
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
        </div><!--/.navbar-collapse -->
TOPRIGHT;
        }
        
        $nav = <<<NAVBAR
                    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://onlinelotbuilder.com/">New Car Scrimmage</a>
        </div>
        $topright
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
NAVBAR;
        echo $nav;
    }
    
    public function footer(){
        $foot = <<<FOOT
                        </div>
                        </div>
                        <div class="container">
                            <!-- Example row of columns -->
                            <hr>
                            <footer>
                               <p style="float: left;">&copy; The Automatons</p>
                               <p style="float: right; align: right;"><a data-toggle="modal" data-target="#GSCCModal">Contact Us</a></p>
                               <p style="float: right; align: right; padding-right:20px;"><a href="dealersignup.php">Dealer Sign Up</a></p>
                             </footer>
                           </div> <!-- /container -->


                       <div id="GSCCModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                           <div class="modal-content">
                             <div class="modal-header">
                               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;  </button>
                               <h4 class="modal-title" id="myModalLabel">Contact Us</h4>
                             </div>
                             <div class="modal-body">
                       <form class="form-horizontal">
                       <fieldset>

                       <!-- Text input-->
                       <div class="form-group">
                         <label class="col-md-4 control-label" for="textinput">Email</label>  
                         <div class="col-md-4">
                         <input id="textinput" name="textinput" type="text" placeholder="you@you.com" class="form-control input-md" required="">

                         </div>
                       </div>

                        <!-- Textarea -->
                        <div class="form-group">
                        <label class="col-md-4 control-label" for="Comments">Comments</label>
                        <div class="col-md-4">                     
                        <textarea class="form-control" id="Comments" name="Comments">Please enter your comments here</textarea>
                        </div>
                        </div>
                        </fieldset>
                    </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>    
FOOT;
        echo $foot;
    }
    
    public function dialog($message='Error'){
        echo "<script>window.alert(\"$message\");</script>";
    }
}
