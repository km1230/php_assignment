<?php 
//============================================================
//load header page
include('./header.php');

//============================================================
//Redirect to index for unauthenticated users
if(!isset($_SESSION['sid'])){
        header('Location: ./index.php');
};

//============================================================
//POST

//Update cart when user remove items
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update'])){
    $numUpdate = count($_POST['remove']);
    for($i = 0; $i < $numUpdate; $i++){
        if($_POST['remove'][$i]==1){
            $removeItem = "DELETE FROM cart WHERE ID='".$_POST['cartID'][$i]."'";
            $conn->query($removeItem);
        };
    };
};

//Check out the cart
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['checkOut'])){
    $numCheckOut = count($_POST['itemID']);
    for($i = 0; $i < $numCheckOut; $i++){
        $cartDetail = "INSERT INTO orders (ID, cafe, itemID, quantity, collecttime, customer, remark, orderdate, price) VALUES ('', '".$_POST['cafe'][$i]."', '".$_POST['itemID'][$i]."', '".$_POST['quantity'][$i]."', '".$_POST['collecttime'][$i]."', '".$_SESSION['sid']."', '".$_POST['remark'][$i]."', '".$orderdate."', '".$_POST['itemPrice'][$i]."')";
        $conn->query($cartDetail);   
    };

    //Update wallet
    $_SESSION['balance'] -= array_sum($_POST['itemPrice']);
    $updateWallet = "UPDATE users SET balance='".$_SESSION['balance']."' WHERE sid='".$_SESSION['sid']."'";
    $conn->query($updateWallet);

    //Clear cart upon checkout
    $clearCart = "DELETE FROM cart WHERE customer='".$_SESSION['sid']."'";
    $conn->query($clearCart); 

    header('Location: ./account.php#orderHistory');    
};

//============================================================
//DELETE
//Delete outdated items from cart when rendering this page
$deleteOutdated = "DELETE FROM cart WHERE customer='".$_SESSION['sid']."' AND date(orderdate) < '".date('Y-m-d')."'";
$conn->query($deleteOutdated);

//============================================================
//GET
//get cart detail	
$getCart = "SELECT * FROM cart WHERE customer='".$_SESSION['sid']."' ORDER BY cafe";
$cart = $conn->query($getCart);
while($row = $cart->fetch_assoc()){
    $cartID[] = $row['ID'];
    $date[] = $row['orderdate'];
    $cafe[] = $row['cafe'];
    $itemID[] = $row['itemID'];
    $quantity[] = $row['quantity'];
    $time[] = $row['collecttime'];
    $remark[] = $row['remark'];
    $itemPrice[] = $row['price']*$row['quantity'];
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
	<img src='./img/cart.jpg' alt='My Cart'>
</div>

<!--Banner in middle-->
<div class='heading'>My Cart</div>

<div class='rowspace'></div>

<!--Breadcrumb-->
<div class='mb-3'>
    <ul class='breadcrumb'>
        <li>
            <a href='./index.php'>::Index::</a>
        </li>
        <li>
            <a href='./cart.php'>::MyCart::</a>
        </li>
    </ul>
</div>

<!--Main content of the page-->
<div class='container'>
    <div class='row mb-1'>
        <div class='col-12 strong whiteTitle mb-1'>My Cart</div>
        <div class='col-12'>
            <?php
                //Display cart details if there is any items
                if($numItem > 0){
                    echo"
                    <form method='POST' action=".htmlspecialchars($_SERVER['PHP_SELF']).">
                        <center>
                            <div class='scrollTable'>
                                <table class='mb-3'>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Cafe</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Remark</th>
                                            <th>Price($)</th>
                                            <th>Collection Time</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>";

                                    for($i = 0; $i < $numItem; $i++){
                                        $cafeName = strtoupper($cafe[$i][0]).substr($cafe[$i], 1, strlen($cafe[$i]));
                                        echo"<tr>
                                            <td>".$date[$i]."</td>
                                            <td>".
                                                $cafeName.
                                                "<input type='hidden' name='cafe[]' value='".$cafe[$i]."'>
                                            </td>
                                            <td>".
                                                $itemName[$i].
                                                "<input type='hidden' name='itemID[]' value='".$itemID[$i]."'>
                                            </td>
                                            <td>".
                                                $quantity[$i].
                                                "<input type='hidden' name='quantity[]' value='".$quantity[$i]."' class='quantity'>
                                            </td>
                                            <td>".$remark[$i].
                                                "<input type='hidden' name='remark[]' value='".$remark[$i]."'>
                                            </td>
                                            <td>".
                                                $itemPrice[$i].
                                                "<input type='hidden' name='itemPrice[]' value='".$itemPrice[$i]."' class='itemPrice'>
                                            </td>
                                            <td>".date('H:i', strtotime($time[$i])).
                                            "<input type='hidden' name='collecttime[]' value='".$time[$i]."'>
                                            </td>
                                            <td>
                                                <div class='removeBox'>
                                                    <input type='checkbox' name='remove[]' value='0' class='remove'>
                                                    <label for='remove' class='removeLabel'></label>
                                                    <input type='hidden' name='cartID[]' value='".$cartID[$i]."'>
                                                </div>
                                            </td>
                                        </tr>";
                                    };

                            echo "</tbody>
                                </table>
                            </div>
                            
                            <div class='row mb-1 checkmoney'>
                                <!--Showing the wallet-->
                                <div class='col-12 mb-1'>
                                    <div class='row'>
                                        <div class='col-2'>Wallet: </div>
                                        <div class='col-2'>$".
                                            $_SESSION['balance'].
                                        "</div>
                                        <div class='col-8'></div>
                                    </div>
                                </div>    

                                <!--Showing the total price-->
                                <div class='col-12'>
                                    <div class='row'>
                                        <div class='col-2'>Total: </div>
                                        <div class='col-2'>$".
                                            array_sum($itemPrice).
                                        "</div>
                                        <div class='col-8'></div>
                                    </div>
                                </div>
                            </div>

                            <!--Showing the balance on check out-->
                            <div class='row mb-1'>
                                <div class='col-12 checkmoney' id='balance'>
                                    <div class='row'>
                                        <div class='col-2'>Balance on Check Out: </div>
                                        <div class='col-2' id='checkOutBalance'>$".
                                            ($_SESSION['balance'] - array_sum($itemPrice)).
                                        "</div>
                                        <div class='col-8'></div>
                                    </div>
                                </div>
                            </div>

                            <!--Update cart when user remove items-->
                            <input type='submit' class='btn btn-warning mb-1' name='update' value='&olarr; Update Cart'>

                            <!--Check out and clear cart-->
                            <input type='submit' class='btn btn-info mb-1' name='checkOut' value='&#36; Check Out' id='checkOut'>

                            <!--Link to recharge wallet-->
                            <a href='./account.php#recharge'>
                                <button type='button' class='btn btn-success mb-1'>&rlarr; Recharge</button>
                            </a>

                            <!--Reset page-->
                            <button type='button' class='btn btn-secondary mb-1'>Reset</button>
                        </center>
                    </form>
                    ";      
                } else {
                    echo "<center class='alert strong slideIn mb-1'>Your cart is empty.</center>";
                };
            ?>
        </div>
    </div>              
</div>

<script src='./js/reg.js'></script>
<script src='./js/manage.js'></script>
<script>
/*
lock check out button if 
any checkbox for removing items is checked or
the check out balance is negative
*/
const remove = document.querySelectorAll('.remove');
const checkOut = document.querySelector('#checkOut');
const balance = document.querySelector('#balance');
const checkOutBalance = document.querySelector('#checkOutBalance');
let lock = false;

if(<?php echo $numItem;?> > 0){
    setInterval(()=>{
        for(let r of remove){
            if(r.checked || parseFloat(checkOutBalance.innerText) < 0){
                lock = true;
                break;
            } else {
                lock = false
            }
        };
        if(lock){
            checkOut.disabled = true;
            checkOut.classList.remove('btn-info');
            checkOut.classList.add('btn-disabled');
        } else {
            checkOut.disabled = false;
            checkOut.classList.remove('btn-disabled');
            checkOut.classList.add('btn-info');
        }
    },300)  
};

//change colour for negative balance
if(parseFloat(checkOutBalance.innerText) < 0){
    balance.style.borderColor = '#d02c2c';
    balance.style.color = '#d02c2c'
}
</script>
<?php include('./footer.php');?>