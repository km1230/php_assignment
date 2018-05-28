<?php
//============================================================
//load header page
include('./header.php');

//============================================================
//POST
//Post if user is not logged in	
if($_SERVER['REQUEST_METHOD']==='POST' && !isset($_SESSION['sid'])){
	
	//register
	$reg = $conn->prepare("INSERT INTO users (sid, password, name, email, phone, card) VALUES (?, ?, ?, ?, ?, ?)");
	$reg->bind_param('ssssss',
		$_POST['sid'],
		sha1($_POST['password']),
		$_POST['name'],
		$_POST['email'],
		$_POST['phone'],
		$_POST['card']
	);
	$reg->execute();
	$reg->close();

//============================================================
//GET
	//get user after registration
	$getUser = "SELECT * FROM users WHERE sid='".$_POST['sid']."'";
	$getUserResult = $conn->query($getUser);
	$row = $getUserResult->fetch_assoc();
	$_SESSION = array(
        'sid'=>$row['sid'], 
        'name'=>$row['name'], 
        'email'=>$row['email'], 
        'phone'=>$row['phone'],
        'card'=>$row['card'],
        'balance'=>$row['balance'],
        'permission'=>$row['permission']
      );	
};

//============================================================
?>

<!-- top menu bar-->
<?php include('./nav.php');?>

<!--Picture of the page-->
<div id='carousel'>
	<img src='./img/signup.jpg' alt='Sign up'>
</div>
	<!--Banner in middle-->
<div class='heading'>Sign Up Here!</div>

<div class='rowspace'></div>
<!--Main content of the page-->
<div class='container strong'>

	<div class='row alert mb-3'>
		<div class='col-lg-2'></div>
    	<div class='col-12 col-lg-8 strong slideIn'>
    		
    		<?php 
    			//Remind to login if visitor has an account
	    		if(!isset($_SESSION['sid'])){
	    			echo "<center>
	    					Already Have an account? <a href='./index.php#login'>::Login Here!::</a>
	    				</center>";
	    		} else {
	    			//Display link to MyAccount to authenticated users
	    			echo "<center>
	    				You have registered already! Click <a href='./account.php'>::here::</a> to view your account.
	    				</center>";
	    		};
    		?>

    	</div>
    	<div class='col-lg-2'></div>
    </div>

    <!--Sign up form for unauthenticated users only-->
    <?php 
	    if(!isset($_SESSION['sid'])){
	    	echo "<div class='row'>
			<div class='col-lg-2'></div>
	    	<div class='col-12 col-lg-8' id='signupForm'>
	    		<form method='POST' action='".$_SERVER['PHP_SELF']."'>
			    	<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='sid'>ID:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='text' name='sid' id='sid' placeholder='Student / Staff ID' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='sid_error' class='alert'></span>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='password'>Password:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='password' name='password' id='password' placeholder='6-12 chars password' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<meter id='pw_meter' min='0' max='100' low='50' high='70' optimum='100' value=''></meter>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='pw_error' class='alert'></span>
			    		</div>
			    	</div>";
			    echo"<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='password2'>Confirm Password:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='password' id='password2' placeholder='Confirm password' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='pw2_error' class='alert'></span>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='name'>Name:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='text' name='name' id='name' placeholder='Preferred name' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='name_error' class='alert'></span>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='email'>Email:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='email' name='email' id='email' placeholder='e.g. xxx@utas.edu.au' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='email_error' class='alert'></span>
			    		</div>
			    	</div>";
			    echo "<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='phone'>Phone:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='tel' name='phone' id='phone' placeholder='e.g. 04xxxxxxxx' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='phone_error' class='alert'></span>
			    		</div>
			    	</div>";
			    echo "<div class='row mb-1'>
			    		<div class='col-3'>
			    			<label for='card'>Credit Card#:</label>
			    		</div>
			    		<div class='col-9'>
			    			<input type='text' name='card' id='card' placeholder='card# without hyphen(-)' required='required'>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span class='cardImg strong'>
			    				We accept: 
			    				<img src='./img/visa.png' alt='visa card'>
  								<img src='./img/master.png' alt='master card'>
   			    				<img src='./img/discover.png' alt='discover card'>
			    			</span>
			    		</div>
			    	</div>
			    	<div class='row mb-1'>
			    		<div class='col-3'></div>
			    		<div class='col-9'>
			    			<span id='card_error' class='alert'></span>
			    		</div>
			    	</div>
		    		<a href='./index.php'><button type='submit' class='btn-info mb-1' id='submit'>Sign Up!</button></a>
		    		<button type='button' class='btn btn-secondary mb-1'>Reset</button>
		    	</form>
	    		</div>
	    	<div class='col-lg-2'></div>
	    </div>";
	    };
    ?>
	
</div>

<script src='./js/reg.js'></script>

<?php include('./footer.php') ?>