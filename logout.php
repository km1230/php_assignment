<?php 
//============================================================
session_start();
if(!isset($_SESSION['sid'])){
  header('Location: ./index.php');
};

//clear and destroy session detail
session_destroy();

//============================================================
?>

<!doctype html>
<html lang="en">
  <head>
    <link rel='stylesheet' href='./css/mystyle.css'>
    <!--Viewport for responsive design-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--redirecting to homepage in 5s-->
    <meta http-equiv="refresh" content="5; url='./index.php'">
    <script src='https://code.jquery.com/jquery-3.3.1.min.js'></script>
    <!--master jquery for menu bar and responsive design-->
    <script src='./js/jquery.js'></script>
    <title>Y.E.O.M. Pty. Ltd.</title>
  </head>
  <body>

    <!--Top menu bar-->
    <div id='menu'>
      <nav>
        <a href='./index.php' id='logo'><img src='./img/cafe.jpg' alt='Site Logo'></a>
        <a href='./index.php' class='nav-link' id='home'>Home</a>
        <a href='./lazenbys.php' class='nav-link' id='lazenbys'>Lazenbys</a>
        <a href='./ref.php' class='nav-link' id='theRef'>The-Ref</a>
        <a href='./trade.php' class='nav-link' id='tradeTable'>Trade-Table</a>
        <a href='./registration.php' class='nav-link' id='reg'>Sign Up!</a>
      </nav>

      <!--Burger button for responseive design-->
      <div>
        <a href='#' id='menuToggle'>&#9776;</a>
      </div>
    </div>

    <div class='rowspace'></div>
    
    <!--Picture of the page-->  
  	<div id='carousel'>
  	    <img src='./img/utas.jpg' alt='Index Utas picture'>
  	</div>
    
    <!--Banner in middle-->
  	<div class='heading'>See you next time!</div>

  	<div class='rowspace'></div>
  
    <!--Main content of the page-->
  	<div class='container'>
  		<div class='row'>
  			<div class='col-12 success strong large whiteTitle'>You have logged out successfully!</div>
  			<div class='col-12 mt-3 mb-3 strong'>
  				<p>Redirecting to home page in ...
  				<span id='redirect'>6</span>s</p>
  			</div>
  		</div>
  	</div>

   <div class='rowspace'></div>

	<div id='footer'>
	  <span>Y.E.O.M. Pty. Ltd. &copy; 2018</span>
	</div>      

	<script>
  //countedown for redirecting page
	let countdown = 6;
	setInterval(()=>{
		if(countdown > 0){countdown--;};
    $('#redirect').html(countdown).css('color', '#d02c2c');
	}, 1000);
  </script>
  </body>
</html>