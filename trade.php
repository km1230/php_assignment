<?php 
//============================================================
//load header page
include('./header.php');

//============================================================
//POST

//Adding items to cart
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['addToCart'])){
	//clean data
	function cleanData($data) {
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  $data = trim($data);
	  return $data;
	};

	$numOrder = count($_POST['itemID']);	
	for($i = 0; $i < $numOrder; $i++){
		$remark = cleanData($_POST['remark'][$i]);
		$orderDetail = $conn->prepare("INSERT INTO cart (cafe, itemID, quantity, collecttime, customer, remark, orderdate, price) VALUES ('trade', ?, ?, ?, ?, ?, ?, ?)");
		$orderDetail->bind_param('sissssd',
			$_POST['itemID'][$i],
			$_POST['quantity'][$i],
			$_POST['collecttime'],
			$_SESSION['sid'],
			$remark,
			$orderdate,
			$_POST['itemPrice'][$i]
		);

        //only add item with quantity larger than 0 to cart
        if($_POST['quantity'][$i]>0){
          $orderDetail->execute();
          $orderDetail->close();
        };
	};
};

//============================================================
//GET

//Get opening hours
$getOpen = "SELECT * FROM open WHERE ID='3'";
$getOpenResult = $conn->query($getOpen);
while($row = $getOpenResult->fetch_assoc()){
	$openhour = strtotime($row['openhour']);
	$closehour = strtotime($row['closehour']);
};

//GET menu items
$getItem = "SELECT * FROM trade WHERE ".$weekday[($nextDay)]."=1 AND manselect=1";
$getItemResult = $conn->query($getItem);
while($row = $getItemResult->fetch_assoc()){
	$menuName[] = $row['item'];
	$menuPrice[] = $row['price'];
	$menuID[] = $row['ID'];
	$numMenu += 1;
};

//============================================================
//Generate options for collection time
for($h = date('H', $openhour); $h < date('H', $closehour); $h++){
	if(strlen($h)<2){
		$h = '0'.$h;	
	};
	$collecthour[] = $h;
};
for($m = 0; $m < 4; $m++){
	if($m == 0){
		$collectmin[] = '00';
	} else {
		$collectmin[] = $m * 15;
	};
};
foreach($collecthour as $h){
	foreach($collectmin as $m){
		$collectTime[] = $h.":".$m;
	};
};

//============================================================
//Delete outdated items from cart when loading page
$deleteOutdated = "DELETE FROM cart WHERE customer='".$_SESSION['sid']."' AND date(orderdate) <= '".date('Y-m-d')."'";
$conn->query($deleteOutdated);

//============================================================
//GET cart detail	
$getCart = "SELECT * FROM cart WHERE customer='".$_SESSION['sid']."' ORDER BY cafe";
$cart = $conn->query($getCart);
while($row = $cart->fetch_assoc()){
    $cafe[] = $row['cafe'];
    $itemID[] = $row['itemID'];
    $quantity[] = $row['quantity'];
    $itemPrice[] = $row['price']*$row['quantity'];
    $itemCollectTime = $row['collecttime'];
    $numCart += 1;
};

//GET item names in cart from each cafe
for($i = 0; $i < $numCart; $i++){
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
	<img src='./img/wine.jpg' alt='Trade Table'>
</div>

<!--Banner in middle-->
<div class='heading' id='tradeTableHeading'>Trade Table</div>

<div class='rowspace'></div>

<!--Breadcrumb-->
<div class='mb-3'>
	<ul class='breadcrumb'>
		<li>
			<a href='./index.php'>::Index::</a>
		</li>
		<li>
			<a href='./trade.php'>::Trade Table Menu::</a>
		</li>
	</ul>
</div>

<!--Login reminder for ordering-->
<?php
	if(!isset($_SESSION['sid'])){
		echo "<center class='alert strong slideIn mb-1'>
				Please&nbsp;<a href='./index.php#login'>::login::</a>&nbsp;to make orders
			</center>";
	};
?>

<!--Main content of the page-->
<div class='container'>
	<div class='row'>
		<!--Opening hours-->
		<div class='col-12 strong openhour mb-1'>
			<?php echo "Open at ".date('H:i', $openhour)." - ".date('H:i', $closehour)." (Mon-Fri)";?>
		</div>
		<div class='col-lg-7 col-12'>
			<p class='strong mb-1 whiteTitle'>Menu For Coming Day</p>

			<!--Menu table for ordering, hidden option for unauthenticated users-->
			<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
				<table class='order-table mb-1'>
					<thead>
						<tr>
							<th>Items</th>
							<th>Price($)</th>
							<?php
								if(isset($_SESSION['sid'])){
									echo "
									<th>Quantity</th>
									<th>Remarks</th>
									";
								};
							?>
						</tr>
					</thead>
					<tbody>

						<?php 
							//Display option to authenticated users only
							if(isset($_SESSION['sid'])){
								for($i = 0; $i < $numMenu; $i++){
									echo "<tr>
									<td>".$menuName[$i]."</td>
									<td>";
									if(preg_match('/^US/', $_SESSION['sid'])){
										echo $menuPrice[$i]*0.9.
										"<input type='hidden' name='itemPrice[]'' value='".($menuPrice[$i]*0.9)."'>";
									} else {
										echo $menuPrice[$i].
										"<input type='hidden' name='itemPrice[]'' value='".$menuPrice[$i]."'>";
									};
									echo "
									</td>
									<td>
										<select name='quantity[]' class='quantity' required>
											<option value='0'>0</option>
											<option value='1'>1</option>
											<option value='2'>2</option>
											<option value='3'>3</option>
											<option value='4'>4</option>
										</select>
									</td>
									<td>
										<input type='text' name='remark[]'>
										<input type='hidden' name='itemID[]' value='".$menuID[$i]."'>
									</td>
									</tr>";
								};

							} else {

								//Display item detail to unauthenticated users
								for($i = 0; $i < $numMenu; $i++){
									echo "<tr>
									<td>".$menuName[$i]."</td>
									<td>".$menuPrice[$i]."</td>
									</tr>";
								};
							};
						?>
					</tbody>
				</table>

				<?php
					//Display option to authenticated users only
					if(isset($_SESSION['sid'])) {
						echo "<div class='row mb-3'>
								<div class='col-lg-2'></div>
								<div class='col-lg-8'>
									<div class='row greyTitle'>
										<div class='col-4 strong'>Collection Time: </div>
										<div class='col-8'>
											<select name='collecttime' class='collect' required>
													<option value =''></option>";

													foreach($collectTime as $t){
														if(
															((strtotime($t) - $openhour) >= 1800) && 
															(($closehour - strtotime($t)) >= 3600)
														){
															echo "<option value='$t'>$t</option>";
														};
													};

										echo "</select>
										</div>
									</div>
								</div>
								<div class='col-lg-2'></div>
							</div>";

						//display total cost of the items to be ordered
						echo "<div class='row mb-1'>
							<div class='col-12 mb-1 checkmoney'>
								<div class='row'>
									<div class='col-2'>Sub-total: $</div>
									<div class='col-2' id='calculator'></div>
									<div class='col-8'></div>
								</div>
							</div>					
						</div>";
					};
				?>

				<!--Hidden button for unauthenticated users-->
				<?php
					if(isset($_SESSION['sid'])){
						echo "<div class='row'>
						<div class='col-12'>
							<input type='submit' class='btn-info mb-1' id='submitOrder' name='addToCart' value='&#10004; Add To Cart'>
						</div>";

		                //reload page
						echo "<div class='col-12'>
							<button type='button' class='btn-secondary mb-1'>Reset</button>
						</div>
						</div>";
					};
				?>
			</form>

			<!--buttons for staff-->
			<?php
				//Button to view orders for cafe staff and manager
				if(preg_match('/3$/', $_SESSION['permission']) || $_SESSION['permission']==400){
					echo "<div class='col-12'>
					<a href='./trade_order.php'><button type='button' class='btn-success mb-1'>&#9778; View Orders</button></a>
					</div>";
				};


				//Button to manager page for manager and director
				if($_SESSION['permission']==303 || $_SESSION['permission']==400){
					echo "<div class='col-12'>
					<a href='./trade_manager.php'><button type='button' class='btn-warning mb-1'>&#8499; Manager Page</button></a>
					</div>";
				};

				//Button to master page for director only
				if($_SESSION['permission']==400){
					echo "<div class='col-12'>
					<a href='./trade_master.php'><button type='button' class='btn-alert mb-1'>&#10000; Master Page</button></a>
					</div>";
				} 
			?>
		</div>
		<div class='col-lg-1'></div>

		<!--Display brief cart detail-->
		<div class='col-lg-4 col-12' id='sideCart'>
			<p class='strong mb-1 whiteTitle'>My Cart</p>
			<?php
				//Hide cart detail to unauthenticated users
				if(!isset($_SESSION['sid'])){
					echo "<center class='alert strong slideIn mb-1'>
							Please&nbsp;<a href='./index.php#login'>::login::</a>&nbsp;to view your cart.
						</center>";

				} elseif($numCart == 0) {
					//Authenticated users with empty cart
					echo "<center class='alert strong slideIn mb-1'>Your cart is empty.</center>
						<!--link to MyCart-->
						<a href='./cart.php'>
							<button type='button' class='btn btn-info'>&#36; View MyCart</button>
						</a>";

				} else {
					//Authenticated users with cart detail
					echo "
					<table class='mb-1' id='sideCartTable'>
						<thead>
							<tr>
								<th>Item</th>
								<th>Quantity</th>
								<th>Price($)</th>
								<th>Collection Time</th>
							</tr>
						</thead>
						<tbody>";
							
							for($i = 0; $i < $numCart; $i++){
								echo "<tr>
									<td>".$itemName[$i]."</td>
									<td>".$quantity[$i]."</td>
									<td>".$itemPrice[$i]."</td>
									<td>".date('H:i', strtotime($itemCollectTime[$i]))."</td>
								</tr>";
							};
							
					echo "</tbody>
					</table>
					";

					//Balance and wallet detail
					echo "  
                        <div class='row mb-1 checkmoney'>
                            <!--Showing the wallet-->
                            <div class='col-12 mb-1'>
                                <div class='row'>
                                    <div class='col-2'>Wallet: </div>
                                    <div class='col-8'>$".
                                    	$_SESSION['balance'].
                                    "</div>
                                </div>
                            </div>    

                            <!--Showing the total price-->
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-2'>Total: </div>
                                    <div class='col-8'>$".
                                    	array_sum($itemPrice).
                                    "</div>
                                </div>
                            </div>
                        </div>

                        <!--Showing net balance-->
                        <div class='row mb-1'>
                            <div class='col-12 checkmoney' id='balance'>
                                <div class='row'>
                                    <div class='col-6'>Balance on Check Out: </div>
                                    <div class='col-6' id='checkOutBalance'>$".
                                    	($_SESSION['balance'] - array_sum($itemPrice)).
                                    "</div>
                                </div>
                            </div>
                        </div>
                        
                        <!--link to MyCart-->
						<a href='./cart.php'>
							<button type='button' class='btn btn-info'>&#36; View MyCart</button>
						</a>";
				};
			?>
		</div>
	</div>
</div>

<script>
//color of opening hour
let now = new Date();
if(
	(<?php echo date('w');?> > 0) &&
	(<?php echo date('w');?> < 6) &&
	(now.getHours() > <?php echo date('H', $openhour);?>) && 
	(now.getHours() < <?php echo date('H', $closehour);?>)
){
	$('.openhour').
	addClass('success').
	css('border-color', '#28a745');
	$('#openClose').text('Opening');
} else {
	$('.openhour').
	addClass('alert').
	css('border-color', '#d02c2c');
	$('#openClose').text('Closed');
};

//calculate order sub-total
let subtotal = [];
function calTotal(){
	let t = subtotal.reduce((a,b) => (a+b));
	$('#calculator').text(t.toFixed(2));
};
$('.quantity').each(function(index){
	$(this).change(function(){
		subtotal[index] = parseFloat($(this).val()) * parseFloat($(this).parent().prev().text());
		calTotal();
	});
});

//lock submit button until total quantity is not 0
setInterval(()=>{
	let totalQuantity = 0;
	document.querySelectorAll('.quantity').forEach(q=>{
		totalQuantity += Number(q.value);
	});
	if(totalQuantity==0){
		$('#submitOrder').prop('disabled', true);
		$('#submitOrder').removeClass('btn-info');
		$('#submitOrder').addClass('btn-disabled');
	} else {
        $('#submitOrder').prop('disabled', false);
		$('#submitOrder').removeClass('btn-disabled');
		$('#submitOrder').addClass('btn-info');
    }
}, 300);

//page reload button
$('.btn-secondary').click(function(){
	location.reload();
});

//change colour for negative balance
const checkOutBalance = document.querySelector('#checkOutBalance');
if(parseFloat(checkOutBalance.innerText) < 0){
    balance.style.borderColor = '#d02c2c';
    balance.style.color = '#d02c2c'
}
</script>

<?php include('./footer.php');?>