<?php
//============================================================
//load header page
include('header.php');

//============================================================
//redirect to cafe menu user is if not manager nor director
if($_SESSION['permission']!=400 && $_SESSION['permission']!=301){
	header('Location: ./lazenbys.php');
};

//============================================================
//POST

if($_SERVER['REQUEST_METHOD']==='POST'){
    
    //update opening hour
    if(isset($_POST['updateHour'])){
	    $oh = $_POST['openhour'].":".$_POST['openmin'];
	    $ch = $_POST['closehour'].":".$_POST['closemin'];
		$updatetime = "UPDATE open SET openhour='".$oh."', closehour='".$ch."' WHERE ID='1'";
		$conn->query($updatetime);
	};

	//select item to show on menu
	if(isset($_POST['updateMenu'])){
		$selectError = '';
		$numItem = count($_POST['itemID']);
		$manSelect = array_count_values($_POST['manselect']);

		if($manSelect[1] == 0){
			$selectError = "At least 1 item should be selected for the menu";	
		} else {
			for($i = 0;$i < $numItem; $i++){
				$updateMenu = "UPDATE lazenbys SET manselect='".$_POST['manselect'][$i]."' WHERE ID='".$_POST['itemID'][$i]."'";
		        $conn->query($updateMenu);
			};
	    };
	};
};

//============================================================
//GET

//get item list
$getItem = "SELECT * FROM lazenbys WHERE $weekday[$nextDay]=1";
$getItemResult = $conn->query($getItem);

//get opening hour
$getTime = "SELECT * FROM open WHERE ID='1'";
$getTimeResult = $conn->query($getTime);
while($row = $getTimeResult->fetch_assoc()){
	$openhour = strtotime($row['openhour']);
	$closehour = strtotime($row['closehour']);
};

//============================================================
//variables for forms
$day = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri');
for($h=0;$h<24;$h++){
	if(strlen($h) < 2){
		$h = '0'.$h;
	};
	$hour[] = $h;
};
for($m = 0; $m < 4; $m++){
	if($m == 0){
		$min[] = '00';
	} else {
		$min[] = $m * 15;
	};
};

//============================================================
?>

<!-- top menu bar-->
<?php include('./nav.php');?>

<!--Picture of the page-->
<div id='carousel'>
	<img src='./img/procure.jpg' alt='Manage and Master'>
</div>

<!--Banner in middle-->
<div class='heading'>Lazenbys Manager Page</div>

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
			<a href='./lazenbys_manager.php'>::Lazenbys Manager Page::</a>
		</li>
	</ul>
</div>

<!--Main content of the page-->
<div class='container'>

	<form method='POST' action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>'>
	
	<!--Change opening hours-->
	<div class='row mb-3'>
		<div class='col-12 strong whiteTitle mb-1'>Set Opening Hours</div>
		<div class='col-lg-2 mb-1'></div>
		<div class='col-12 col-lg-8 mb-1'>
			<table>
				<thead>
					<tr>
						<td colspan='2'>Open at</td>
						<td colspan='2'>Close at</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Hours</td>
						<td>Minutes</td>
						<td>Hours</td>
						<td>Minutes</td>
					</tr>
					<tr>
						<td>
							<select name='openhour'>
								<?php foreach($hour as $h){
									echo "<option value='".$h."'>".$h."</option>";
								};?>
							</select>
						</td>
						<td>
							<select name='openmin'>";
								<?php foreach($min as $m){
									echo "<option value='".$m."'>".$m."</option>";
								};?>
							</select>
						</td>
						<td>
							<select name='closehour'>";
								<?php foreach($hour as $h){
									echo "<option value='".$h."'>".$h."</option>";
								};?>
							</select>
						</td>
						<td>
							<select name='closemin'>";
								<?php foreach($min as $m){
									echo "<option value='".$m."'>".$m."</option>";
								};?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='col-lg-2 mb-1'></div>
		<div class='col-12'>
			<input type='submit' name='updateHour' class='btn btn-warning' value='&#9728; Update Opening Hours'>
		</div>
	</div>


	<!--select items for the menu of following day-->
	<div class='row'>
		<div class='col-12 strong whiteTitle mb-1'>Select Items For Following Day</div>
        <?php
            if($selectError != ''){
              echo "<center class='alert strong slideIn mb-1'>".$selectError."</center>";
            };
        ?>
		<div class='col-12 mb-1'>
			<table class='master-table'>
				<thead>
					<tr>
						<th class='master-table-left'>Items</th>
						<th>Prices($)</th>
						<th>Days(Assigned by Director)</th>
						<th>Select to menu</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($getItemResult->num_rows > 0){
							while($row = $getItemResult->fetch_assoc()){
								echo "<tr>
								<td>".
									$row['item'].
									"<input type='hidden' name='itemID[]' value='".$row['ID']."'>
								</td>
								<td>".$row['price']."</td>
								<td>
									<div class='row'>";		
								foreach($day as $d){
									if($row[strtolower($d)] == '1'){
										echo "<div class='col-12'>".
										$d.
										"</div>";
									};
								};
								echo "</div>
								</td>
								<td>
									<div class='manSelectBox'>
										<input type='checkbox' name='manselect[]' class='manSelect' value=".$row['manselect'].">
										<label for='manSelect'></label>
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
			<input type='submit' name='updateMenu' id='update' class='btn btn-warning' value='&olarr; Update Menu Items'>
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
<script>
//show current selection of opening hour on page load
const openhour = document.querySelectorAll('select[name="openhour"] > option');
const openmin = document.querySelectorAll('select[name="openmin"] > option');
const closehour = document.querySelectorAll('select[name="closehour"] > option');
const closemin = document.querySelectorAll('select[name="closemin"] > option');
openhour.forEach(x=>{
	if(x.value == <?php echo date('H', $openhour);?>){
		x.selected = true
	};
});
openmin.forEach(x=>{
	if(x.value == <?php echo date('i', $openhour);?>){
		x.selected = true
	};
});
closehour.forEach(x=>{
	if(x.value == <?php echo date('H', $closehour);?>){
		x.selected = true
	};
});
closemin.forEach(x=>{
	if(x.value == <?php echo date('i', $closehour);?>){
		x.selected = true
	};
});
</script>

<?php include('./footer.php');?>