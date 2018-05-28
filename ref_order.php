<?php
//============================================================
//load header page
include('./header.php');

//============================================================
//restrict access to staff and manager of this cafe only
if($_SESSION['permission']<200 || preg_match('/1$/', $_SESSION['permission']) || preg_match('/3$/', $_SESSION['permission'])){
	header('Location: ./ref.php');
};

//============================================================
//POST
//Update order history when staff complete orders
if($_SERVER['REQUEST_METHOD']==='POST'){
	for($i = 0; $i < count($_POST['orderID']); $i++){
        if($_POST['completed'][$i]==1){
		 $completedOrder = "DELETE FROM orders WHERE ID='".$_POST['orderID'][$i]."'";
		 $conn->query($completedOrder);
        };
	};
};

//============================================================
//GET	
//get order
$getOrder = "SELECT * FROM orders WHERE cafe='ref' AND orderdate='".date('Y-m-d')."' ORDER BY collecttime";
$getOrderResult = $conn->query($getOrder);
while($row = $getOrderResult->fetch_assoc()){
	$orderID[] = $row['ID'];
	$itemID[] = $row['itemID'];
	$quantity[] = $row['quantity'];
	$collecttime[] = strtotime($row['collecttime']);
	$customer[] = $row['customer'];
	$remark[] = $row['remark'];		
	$numItem += 1;
};
if(!isset($numItem)){$numItem = 0;};

//get item name
for($i = 0; $i < $numItem; $i++){
	$getItemName = "SELECT * FROM ref WHERE ID='".$itemID[$i]."'";
	$getItemNameResult = $conn->query($getItemName);
	$row = $getItemNameResult->fetch_assoc();
	$itemName[] = $row['item'];
};

//get customer name
for($i = 0; $i < $numItem; $i++){
	$getCustomer = "SELECT * FROM users WHERE sid='".$customer[$i]."'";
	$getCustomerResult = $conn->query($getCustomer);
	$row = $getCustomerResult->fetch_assoc();
	$customerName[] = $row['name'];
};

//============================================================
?>

<!-- top menu bar-->
<?php include('./nav.php');?>
    
<div id='carousel'>
	<img src='./img/sandwich.jpg' alt='The Ref Order History'>
</div>

<!--Banner in middle-->
<div class='heading'>The Ref Orders History</div>

<div class='rowspace'></div>

<!--Breadcrumb-->
<div class='mb-3'>
	<ul class='breadcrumb'>
		<li>
			<a href='./index.php'>::Index::</a>
		</li>
		<li>
			<a href='./ref.php'>::The Ref Menu::</a>
		</li>
		<li>
			<a href='./ref_order.php'>::The Ref View Orders::</a>
		</li>
	</ul>
</div>

<!--Main content of the page-->
<div class='container'>

	<div class='row mb-1'>
		<div class='col-12 strong whiteTitle mb-1'>Today's Order</div>
		<div class='col-lg-2'></div>
		<div class='col-lg-8 col-12'>

			<!--Display number of pre-orders-->
			<center class='alert strong slideIn mb-1'>
				<?php 
					echo date('Y-m-d')."<br>";
					echo "We have $numItem pre-order(s)";
				?>
			</center>
			
			<!--Display orders for the same day-->
			<?php
				//Display table if there is any pre-orders
				if($numItem > 0){
					echo "
					<form method='POST' action='".htmlspecialchars($_SERVER['PHP_SELF'])."'>
						<table class='mb-1'>
							<thead>
								<tr>
									<th>Item</th>
									<th>Quantity</th>
									<th>Remark</th>
									<th>Collect</th>
									<th>Customer</th>
									<th>Completed</th>
								</tr>
							</thead>
							<tbody>";
								
									for($i = 0; $i < $numItem; $i++){
										echo"<tr>
											<td>".$itemName[$i]."</td>
											<td>".$quantity[$i]."</td>
											<td>".$remark[$i]."</td>
											<td>".date('H:i', $collecttime[$i])."</td>
											<td>".$customerName[$i]."</td>
											<td>
												<div class='completeBox'>
													<input type='checkbox' name='completed[]' value='0' class='complete'>
													<label for='complete' class='completeLabel'></label>
													<input type='hidden' name='orderID[]' value=".$orderID[$i].">
												</div>
											</td>
										</tr>";
									};
								
						echo "</tbody>
						</table>
						<button type='submit' class='btn btn-warning mb-1' id='update'>&#10226; Update List</button>
						<button type='button' class='btn btn-secondary mb-1'>Reset</button>
					</form>";
				};
			?>
			<a href='./ref.php'>
				<button type='button' class='btn btn-info'>&#8678; Back to Menu</button>
			</a>
		</div>
		<div class='col-lg-2'></div>
	</div>
</div>

<script src='./js/manage.js'></script>
<?php include('./footer.php');?>