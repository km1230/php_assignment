<?php 
//============================================================
//load header page
include('./header.php');

//============================================================
//restrict access to director only
if($_SESSION['permission']!=400){
	header('Location: ./index.php');
};

//============================================================
//POST
if($_SERVER['REQUEST_METHOD']==='POST'){
	
	//For adding new staff
	if(isset($_POST['addstaff'])){

		//clean data for text input
		function cleanData($data) {
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  $data = trim($data);
		  return $data;
		};

		//add new staff
		$setPassword = sha1($_POST['sid']);
		$addUser = $conn->prepare("INSERT INTO users (sid, password, name, email, permission) VALUES (?, ?, ?, ?, '100')");
		$addUser->bind_param('ssss', $_POST['sid'], $setPassword, cleanData($_POST['name']), $_POST['email']);
		$addUser->execute();
		$addUser->close();

	} else {

		//For allocating staff
		$numStaff = count($_POST['sid']);
		$managerError = false;
		$staffError = false;

		//Count different permission level in array
		for($i = 0; $i < $numStaff; $i++){
            if($_POST['remove'][$i] != 1){
                $permission[] = $_POST['permission'][$i] + $_POST['cafe'][$i];
            };
		};
		$permissionArray = array_count_values($permission);

		//check if 1 manager (permission 3xx) in each cafe
		for($i = 301; $i < 304; $i++){
			if(!isset($permissionArray[$i]) || $permissionArray[$i] != 1){
				$managerError = true;
				break;
			};
		};

		//check if at least 1 staff (permission 2xx) in each cafe
		for($i = 201; $i < 204; $i++){
			if(!isset($permissionArray[$i]) || $permissionArray[$i] < 1){
				$staffError = true;
				break;
			};
		};

		//allocate staff if no error
		if(!$managerError && !$staffError){
			for($i = 0; $i < $numStaff; $i++){

				//update staff
				$updateStaff = "UPDATE users SET permission='".($_POST['permission'][$i] + $_POST['cafe'][$i])."' WHERE sid='".$_POST['sid'][$i]."'";
				$conn->query($updateStaff);

				//remove staff
				if($_POST['remove'][$i]==1){
                    $removeStaff = "DELETE FROM users WHERE sid='".$_POST['sid'][$i]."'";
					$conn->query($removeStaff);
				};
			};
		};
	};
};

//============================================================
//GET

//get current staff
$getStaff = "SELECT * from users WHERE permission > 0 AND permission < 400 ORDER BY sid";
$getStaffResult = $conn->query($getStaff);

//============================================================
?>

<!-- top menu bar-->
<?php include('./nav.php');?>

<!--Picture of the page-->
<div id='carousel'>
	<img src='./img/allocate.jpg' alt='Allocate Staff'>
</div>
<!--Banner in middle-->
<div class='heading'>Allocate</div>

<div class='rowspace'></div>

<!--Breadcrumb-->
<div class='mb-3'>
	<ul class='breadcrumb'>
		<li>
			<a href='./index.php'>::Index::</a>
		</li>
		<li>
			<a href='./allocate.php'>::Allocate::</a>
		</li>
	</ul>
</div>

<!--Main content of the page-->
<div class='container'>

	<!--Adding new staff-->
	<div class='row mb-3'>
		<div class='col-12 strong whiteTitle mb-1'>Add New Staff</div>
		<div class='col-lg-2'></div>
		<div class='col-12 col-lg-8'>
			<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
				<div class='row mb-1'>
					<div class='col-12 col-lg-4'>
						<label for='sid' class='strong'>ID: </label>
					</div>
					<div class='col-12 col-lg-8'>
						<input type='text' name='sid' id='sid' required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='sid_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4'>
						<label for='name' class='strong'>Name: </label>
					</div>
					<div class='col-12 col-lg-8'>
						<input type='text' name='name' id='name' required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='name_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-12 col-lg-4'>
						<label for='email' name='email' class='strong'>Email: </label>
					</div>
					<div class='col-12 col-lg-8'>
						<input type='email' name='email' id='email' required>
					</div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8 alert strong' id='email_error'></div>
				</div>
				<div class='row mb-1'>
					<div class='col-lg-4'></div>
					<div class='col-12 col-lg-8'>
						<input type='submit' class='btn btn-warning mb-1' name='addstaff' id='submit' value='&oplus; Add Staff'>
						<button type='button' class='btn btn-secondary'>Reset</button>
					</div>
				</div>
			</form>
		</div>
		<div class='col-lg-2'></div>
	</div>

	<!--Allocating/Removing current staff-->
	<div class='row'>
		<div class='col-12 strong whiteTitle mb-1'>Allocate/Remove Current Staff</div>
		<div class='col-12'>

		<!--Display any error while allocating staff-->
		<?php
			if($staffError){
				echo "<div class='col-12'><center class='alert strong slideIn mb-1'>
				There should be at least 1 manager and 1 staff in a cafe!
				</center></div>";
			} elseif($managerError){
				echo "<div class='col-12'><center class='alert strong slideIn mb-1'><center>
				There should be 1 manager in each cafe!
				</center></div>";
			};
		?>

			<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
				<div class='scrollTable'>
					<table class='mb-1 allocate-table'>
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Staff/Manager</th>
								<th>Cafe</th>
								<th>Remove</th>
							</tr>
						</thead>
						<tbody>
							<?php
								//current staff / managers in different cafes
								while($row = $getStaffResult->fetch_assoc()){
									echo "
									<tr>
										<td>".
											$row['sid'].
											"<input type='hidden' name='sid[]' value='".$row['sid']."'>
										</td>
										<td>".$row['name']."</td>
										<td class='allocateSelect'>";

										//Staff or manager
										echo "<select name='permission[]'>";
											if(preg_match('/^2/', $row['permission'])){
												echo "
												<option value='100'></option>
												<option value='200' selected>Staff</option>
												<option value='300'>Manager</option>
												";
											} elseif(preg_match('/^3/', $row['permission'])) {
												echo "
												<option value='100'></option>
												<option value='300' selected>Manager</option>
												<option value='200'>Staff</option>
												";
											} else {
												echo "
												<option value='100' selected></option>
												<option value='300'>Manager</option>
												<option value='200'>Staff</option>
												";
											}
									echo"</select>
										</td>
										<td class='allocateSelect'>";

										//Which cafe
										echo "<select name='cafe[]'>";
											switch($row['permission'][2]){
												case 1:
													echo "
													<option value='0'></option>
													<option value='1' selected>Lazenbys</option>
													<option value='2'>The Ref</option>
													<option value='3'>Trade Table</option>
													";
													break;

												case 2:
													echo "
													<option value='0'></option>
													<option value='1'>Lazenbys</option>
													<option value='2' selected>The Ref</option>
													<option value='3'>Trade Table</option>
													";
													break;

												case 3:
													echo "
													<option value='0'></option>
													<option value='1'>Lazenbys</option>
													<option value='2'>The Ref</option>
													<option value='3' selected>Trade Table</option>
													";
													break;
												default:
													echo "
													<option value='0' selected></option>
													<option value='1'>Lazenbys</option>
													<option value='2'>The Ref</option>
													<option value='3'>Trade Table</option>
													";
													break;
											};
									echo "</select>
										</td>
										<td>
											<div class='removeBox'>
												<input type='checkbox' name='remove[]' class='remove removeStaff' value='0'>
												<label for='remove' class='removeLabel'></label>
											</div>
										</td>
									</tr>
									";
								};
							?>
						</tbody>
					</table>
				</div>
				<button type='submit' class='btn btn-info mb-1' id='update'>&#9755; Allocate Staff</button>
			</form>
			<button type='button' class='btn btn-secondary'>Reset</button>
		</center>
		</div>
	</div>

</div>

<script src='./js/allocate.js'></script>
<script src='./js/reg.js'></script>

<?php include('./footer.php');?>