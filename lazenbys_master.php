<?php
//============================================================
//load header page
include('./header.php');

//============================================================
//redirect to cafe menu if user is not director
if($_SESSION['permission']!=400){
	header('Location: ./lazenbys.php');
};

//============================================================
//POST

//add new item
if($_SERVER['REQUEST_METHOD']==='POST'){
	//clean data
	function cleanData($data) {
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  $data = trim($data);
	  return $data;
	};

	//Adding New Item
	if(isset($_POST['newItem'])){

		//check duplicated item name
		$duplicated = false;
		$getAll = "SELECT * FROM lazenbys";
		$getAllResult = $conn->query($getAll);
		while($row = $getAllResult->fetch_assoc()){
			if(strtolower($row['item']) == strtolower($_POST['newItem'])){
				$duplicated = true;
				break;
			};
		};

		//add new item if it is not duplicated
		if(!$duplicated){
			$newItem = cleanData($_POST['newItem']);
			$addNewItem = $conn->prepare("INSERT INTO lazenbys (item) VALUES (?)");
			$addNewItem->bind_param('s', $newItem);
			$addNewItem->execute();
			$addNewItem->close();
		} else {
			$duplicatedError = "Item exists already";
		};

	} else {

		//Editing items
		$numItem = count($_POST['item']);
		for($i = 0; $i < $numItem; $i++){
			$item = cleanData($_POST['item'][$i]);
			$editItem = $conn->prepare("UPDATE lazenbys SET item=?, price=?, mon=?, tue=?, wed=?, thu=?, fri=? WHERE ID=?");
			$editItem->bind_param('sdiiiiii',
				$item,
				$_POST['price'][$i],
				$_POST['mon'][$i],
				$_POST['tue'][$i],
				$_POST['wed'][$i],
				$_POST['thu'][$i],
				$_POST['fri'][$i],
				$_POST['itemID'][$i]
			);
			$editItem->execute();
			$editItem->close();
		};

		//Removing items
		$removeError = '';
		$toBeRemove = array_count_values($_POST['removeItem']);
		if($toBeRemove[1] == $numItem){
			$removeError = "There should be at least 1 item in the menu!";
		} else {
			for($i = 0;$i < $numItem; $i++){
				if($_POST['removeItem'][$i] == 1){
					$removeItem = "DELETE FROM lazenbys WHERE ID='".$_POST['itemID'][$i]."'";
					$conn->query($removeItem);
				};
			};	
		};
	};
};

//============================================================
//GET

//get item list
$getItem = "SELECT * FROM lazenbys";
$getItemResult = $conn->query($getItem);

//============================================================
?>

<!-- top menu bar-->
<?php include('./nav.php');?>

<!--Picture of the page-->
<div id='carousel'>
	<img src='./img/procure.jpg' alt='Manage and Master'>  
</div>

<!--Banner in middle-->
<div class='heading'>Lazenbys Master Food &amp; Beverage List</div>

<div class='rowspace'></div>

<!--Breadcrumb-->
<div class='mb-3'>
	<ul class='breadcrumb'>
		<li>
			<a href='./index.php'>::Index::</a>
		</li>
		<li>
			<a href='./lazenbys.php'>::Lazenbys Menu::</a>
		</li>
		<li>
			<a href='./lazenbys_master.php'>::Lazenbys Master Food &amp; Beverage List::</a>
		</li>
	</ul>
</div>

<!--Main content of the page-->
<div class='container'>

	<!--Add items-->
	<div class='row mb-3'>
		<div class='col-12 strong whiteTitle mb-1'>Add Items</div>
		<center class='col-12 mb-1 strong alert slideIn'>
					<?php echo $duplicatedError;?>
		</center>
		<div class='col-lg-2'></div>
		<div class='col-12 col-lg-8'>
		<form method='POST' action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>'>
			<div class='row'>
				<div class='col-9'>
					<input type='text' name='newItem' placeholder='Add item here...' id ='addItem' autofocus>
				</div>
				<div class='col-1'></div>
				<div class='col-2'>
					<button type='submit' class='btn-success' id='addButton'>&#8853; Add</button>
				</div>
			</div>
		</form>
		</div>
		<div class='col-lg-2'></div>	
	</div>

	<!--Current items, director can update/remove items-->
	<div class='row'>
		<div class='col-12 strong whiteTitle mb-1'>Edit / Remove Items</div>
		<?php
			if($removeError != ''){
				echo "<center class='alert strong slideIn mb-1'>".$removeError."</center>";
			};
		?>
		<div class='col-12 mb-1'>
			<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
			<table class='master-table'>
				<thead>
					<tr>
						<th class='master-table-left'>Items</th>
						<th>Prices($)</th>
						<th>Days</th>
						<th>Remove</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($getItemResult->num_rows > 0){
							while($row = $getItemResult->fetch_assoc()){
								echo "<tr>
							<td>
								<input type='text' name='item[]' value='".$row['item']."'>
								<input type='hidden' name='itemID[]' value='".$row['ID']."'>
							</td>
							<td><input type='number' name='price[]' step='0.1' value='".$row['price']."'></td>
							<td>
								<div class='row'>
									<div class='col-8'>Mon</div>
									<div class='col-4'><input type='checkbox' class='day' name='mon[]' value='".$row['mon']."'></div>
									<div class='col-8'>Tue</div>
									<div class='col-4'><input type='checkbox' class='day' name='tue[]' value='".$row['tue']."'></div>
									<div class='col-8'>Wed</div>
									<div class='col-4'><input type='checkbox' class='day' name='wed[]' value='".$row['wed']."'></div>
									<div class='col-8'>Thu</div>
									<div class='col-4'><input type='checkbox' class='day' name='thu[]' value='".$row['thu']."'></div>
									<div class='col-8'>Fri</div>
									<div class='col-4'><input type='checkbox' class='day' name='fri[]' value='".$row['fri']."'></div>
								</div>
							</td>
							<td>
								<div class='removeBox'>
									<input type='checkbox' name='removeItem[]' value='0' class='remove'>
									<label for='remove' class='removeLabel'></label>
								</div>
							</td>
							</tr>";
							};							
						};	
					?>
				</tbody>
			</table>
		</div>
		<div class='col-12 mb-1'>
			<button type='submit' class='btn-warning' id='update'>&#10226; Update</button>
		</div>
		</form>

		<!--Reload page-->
		<div class='col-12 mb-1'>
			<button type='button' class='btn-secondary'>Reset</button>
		</div>

		<!--Back to cafe menu-->
		<div class='col-12 mb-1'>
			<a href='./lazenbys.php'>
				<button type='button' class='btn-info'>&#8678; Back to Menu</button>
			</a>
		</div>
	</div>
</div>

<script src='./js/manage.js'></script>

<?php include('./footer.php');?>