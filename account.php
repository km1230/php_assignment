<?php 
//============================================================
//load header page
	include('./header.php');
	
//============================================================
//Restrict access to authenticated users only	
	if(!isset($_SESSION['sid'])){
		header('Location: ./index.php');
	};

//============================================================
//POST	
	if($_SERVER['REQUEST_METHOD']==='POST'){

		//Recharge wallet
		if(isset($_POST['recharge'])){
			$cardError = '';
			if(strlen($_SESSION['card'])==16){
				$_SESSION['balance'] += $_POST['recharge'];
				$updateWallet = $conn->prepare("UPDATE users SET balance=? WHERE sid=?");
				$updateWallet->bind_param('ds', $_SESSION['balance'], $_SESSION['sid']);
				$updateWallet->execute();
				$updateWallet->close();
			} else {
				$cardError = "Please check your credit card detail.";
			};

		//Update account detail	
		} elseif(isset($_POST['update'])){
			

			//If new password is assigned
			if(($_POST['password']) != ''){
				$updateDetail = $conn->prepare("UPDATE users SET name=?, password=?, phone=?, email=?, card=? WHERE sid=?");
				$updateDetail->bind_param('ssssss', 
						$_POST['name'], 
						sha1($_POST['password']), 
						$_POST['phone'],
						$_POST['email'],
						$_POST['card'],
						$_SESSION['sid']
					);
				$updateDetail->execute();
				$updateDetail->close();
				$_SESSION['name'] = $_POST['name'];
				$_SESSION['password'] = $_POST['password'];
				$_SESSION['phone'] = $_POST['phone'];
				$_SESSION['email'] = $_POST['email'];
				$_SESSION['card'] = $_POST['card'];

			} else {

				//Update user's other details
				$updateDetail = $conn->prepare("UPDATE users SET name=?, phone=?, email=?, card=? WHERE sid=?");
				$updateDetail->bind_param('ssssss', 
						$_POST['name'],  
						$_POST['phone'],
						$_POST['email'],
						$_POST['card'],
						$_SESSION['sid']
					);
				$updateDetail->execute();
				$updateDetail->close();
			};
			$_SESSION['name'] = $_POST['name'];
			$_SESSION['phone'] = $_POST['phone'];
			$_SESSION['email'] = $_POST['email'];
			$_SESSION['card'] = $_POST['card'];
		};
	};

//============================================================
//GET	
	//get order history	
	$getOrderHx = "SELECT * FROM orders WHERE customer='".$_SESSION['sid']."' ORDER BY orderdate";
	$orderHx = $conn->query($getOrderHx);
	while($row = $orderHx->fetch_assoc()){
		$date[] = $row['orderdate'];
		$cafe[] = $row['cafe'];
		$itemID[] = $row['itemID'];
		$quantity[] = $row['quantity'];
		$remark[] = $row['remark'];
		$price[] = $row['price'];
		$numItem += 1;
	};
	
	//get item name from each cafe
	for($i = 0; $i < $numItem; $i++){
		$getItemName = "SELECT * FROM ".$cafe[$i]." WHERE ID='".$itemID[$i]."'";
		$getItemNameResult = $conn->query($getItemName);
		$row = $getItemNameResult->fetch_assoc();
		$itemName[] = $row['item'];
	};

//============================================================
?>

<!-- top menu bar-->
<?php include('./nav.php');?>

<!--Picture of the page-->
<div id='carousel'>
	<img src='./img/account.jpg' alt='My Account'>
</div>

<!--Banner in middle-->
<div class='heading'>My Account</div>

<div class='rowspace'></div>

<!--Breadcrumb-->
<div class='mb-3'>
	<ul class='breadcrumb'>
		<li>
			<a href='./index.php'>::Index::</a>
		</li>
		<li>
			<a href='./account.php'>::MyAccount::</a>
		</li>
	</ul>
</div>

<!--Main content of the page-->
<div class='container'>

	<?php
		//Display error if credit card is not valid
		if($cardError != ''){
			echo "<center class='alert strong slideIn mb-1'>".$cardError."</center>";
		};
	?>

	<!--show wallet balance and recharge-->
	<div class='row mb-3' id='recharge'>
		<div class='col-12 strong whiteTitle mb-1'>My Wallet</div>
		<div class='col-lg-2'></div>
		<div class='col-12 col-lg-8'>
			<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
				<div class='row mb-1'>
					<div class='col-4 strong'>Balance: </div>
					<div class='col-8'>$ <?php echo $_SESSION['balance'];?></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>Top-up: </div>
					<div class='col-12 col-lg-8'>
						<input type='number' step='0.1' name='recharge' required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8'>
						<button type='submit' class='btn btn-warning'>&#8644; Recharge</button>
					</div>
				</div>
			</form>
		</div>	
		<div class='col-lg-2'></div>
	</div>

	<!--show account detail for amendment-->
	<div class='row mb-3'>
		<div class='col-12 strong whiteTitle mb-1'>My Detail</div>
		<div class='col-lg-2'></div>
		<div class='col-12 col-lg-8'>
			<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>Preferred Name: </div>
					<div class='col-12 col-lg-8'>
						<input type='text' name='name' id='name' value="<?php echo $_SESSION['name'];?>" required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='name_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>Current password: </div>
					<div class='col-12 col-lg-8'>
						<input type='password' name='oldpassword' id='oldpassword' required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='oldpassworderror'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>New password: </div>
					<div class='col-12 col-lg-8'>
						<input type='password' name='password' id='password'>
					</div>
				</div>
				<div class='row mb-1'>
		    		<div class='col-lg-4'></div>
		    		<div class='col-12 col-lg-8'>
		    			<meter id='pw_meter' min='0' max='100' low='50' high='70' optimum='100' value=''></meter>
		    		</div>
		    	</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='pw_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>Confirm New password: </div>
					<div class='col-12 col-lg-8'>
						<input type='password' name='password2' id='password2'>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='pw2_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>email: </div>
					<div class='col-12 col-lg-8'>
						<input type='email' name='email' value="<?php echo $_SESSION['email'];?>" id='updateEmail' required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='email_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>Phone: </div>
					<div class='col-12 col-lg-8'>
						<input type='number' name='phone' value="<?php echo $_SESSION['phone'];?>" id='phone' required>
					</div>
		    	</div>
		    	<div class='row mb-1'>
		    		<div class='col-3'></div>
		    		<div class='col-9'>
		    			<span id='phone_error' class='alert'></span>
		    		</div>
		    	</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4 strong'>Credit Card: </div>
					<div class='col-12 col-lg-8'>
						<input type='number' name='card' value="<?php echo $_SESSION['card'];?>" id='card' required>
					</div>
				</div>
				<div class='row mb-1'>
		    		<div class='col-12 col-lg-4 strong'></div>
		    		<div class='col-12 col-lg-8'>
		    			<span class='cardImg strong'>
		    				We accept: 
		    				<img src='./img/visa.png' alt='visa vard'>
 		    				<img src='./img/master.png' alt='master card'>
 		    				<img src='./img/discover.png' alt='discover card'>
		    			</span>
		    		</div>
		    	</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='card_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8'>
						<input type='submit' name='update' class='btn btn-info mb-1' id='submit' value='&#8634; Update Info'>
						<button type='button' class='btn btn-secondary'>Reset</button>
					</div>
				</div>
			</form>
		</div>
		<div class='col-lg-2'></div>
	</div>

	<!--Past order history of customer-->
	<div class='row mb-1'>
		<div class='col-12 strong whiteTitle mb-1' id='orderHistory'>Order History</div>

	<?php
		//display table if there is any order history
		if($numItem > 0){
			echo "<div class='col-12 scrollTable'>
					<table class='mb-1'>
						<thead>
							<tr>
								<th>Date</th>
								<th>Cafe</th>
								<th>Item</th>
								<th>Quantity</th>
								<th>Remark</th>
								<th>Price($)</th>
							</tr>
						</thead>
						<tbody>";
							
						for($i = 0; $i < $numItem; $i++){
							echo"<tr>
								<td>".$date[$i]."</td>
								<td>".$cafe[$i]."</td>
								<td>".$itemName[$i]."</td>
								<td>".$quantity[$i]."</td>
								<td>".$remark[$i]."</td>
								<td>".$price[$i]."</td>
							</tr>";
						};

				echo "</tbody>
					</table>
				</div>";
		} else {
			echo "<center class='alert strong'>
					You have no order history.
				</center>";
		};
	?>
	</div>
</div>

<script src='./js/reg.js'></script>
<script>
//check old password for account modification
$('#oldpassword').keyup(function(){
	if($('#oldpassword').val() != "<?php echo $_SESSION['password'];?>"){
		$('#oldpassworderror').text('Current password not match')
	} else {
		$('#oldpassworderror').text('')
	}
});

//double check new password only if it is assigned
setInterval(()=>{
	if($('#password').val() != '' && $('#password2').val() == ''){
		$('#pw2_error').text('Please confirm new password.');
	} else if($('#password').val()=='' && $('#password2').val() != '') {
		$('#pw2_error').text('Your new password is empty');
	} else if($('#password').val()=='' && $('#password2').val()==''){
		$('#pw_error').text('');
		$('#pw2_error').text('')
	};
	if($('#password').val() == "<?php echo $_SESSION['password'];?>"){
		$('#pw_error').text('New password is same as previous!')
	};
},500);
</script>

<?php include('./footer.php');?>