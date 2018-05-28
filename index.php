<?php 
//============================================================
//load header page
include('./header.php');

//============================================================
//POST
//login user
if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['name']) && isset($_POST['sid'])){
    //clean data
    function cleanData($data) {
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      $data = trim($data);
      return $data;
    };

    $loginID = cleanData($_POST['sid']);
    $loginPW = cleanData($_POST['password']);
    $login = $conn->prepare("SELECT * FROM users WHERE sid=?");
    $login->bind_param('s', $loginID);
    $login->execute();
    $login->store_result();
    
    if($login->num_rows > 0){
      $login->bind_result($sid, $password, $name, $email, $phone, $card, $balance, $permission);
      while($login->fetch()){

        //password validation
        if(sha1($loginPW) == $password){
          $_SESSION = array(
            'sid'=>$sid, 
            'password'=>$loginPW,
            'name'=>$name, 
            'email'=>$email, 
            'phone'=>$phone,
            'card'=>$card,
            'balance'=>$balance,
            'permission'=>$permission
          );
          $loginfail = '';

        } else {
          $loginfail = "Your password does not match!";
        };
      };
    } else {
      $loginfail = "No such ID";
    };
    
    $login->close();
  };

//============================================================  
?>

<!-- top menu bar-->
<?php include('./nav.php');?>

<!--Picture of the page-->
<div id='carousel'>
  <img src='./img/utas.jpg' alt='Index Utas picture'>
</div>

<!--Banner in the middle-->
<div class='heading'>Welcome to our Cafes!</div>

<div class='rowspace'></div>

<!--Main content of the page-->
<div class='container'>
  <div class='row'>

    <!--Left on desktop, appear first in the centre on mobile-->
    <div class='col-12 col-lg-7 mb-1'>
      <div class='row'>
        <div class='col-12 wobble mb-3'>
          <span class='whiteTitle strong large alert'>Let's Pre-order!</span>
        </div>
        <div class='col-12'>
          <div class='row'>
            <div class='col-12 strong mb-1 alert underline'>
              How to order?
            </div>
            <div class='col-12'>
              <img src='./img/steps.jpg' id='step' alt='How to Order'>
            </div>
          </div>
        </div>
      </div>
    </div>
          
    <div class='col-lg-1'></div>
  
    <!--Right on desktop, appear later in the centre on mobile-->
    <div class='col-12 col-lg-4'>
      <div class='row' id='login'>
            
      <!--Login / Logout-->            
      <?php
        //For unauthenticated visitors
        if(!isset($_SESSION['sid'])){
          if($loginfail != ''){
            echo "<p class='alert strong'>".$loginfail."</p>";
          };
          echo"<div id='loginForm' class='col-12'>
              <form method='POST' action='".$_SERVER['PHP_SELF']."'>
              <div class='rowspace'></div>
              <div class='row mb-1'>
                <div class='col-12 mb-3 strong large whiteTitle'><center>Have an account?</center></div>
                <div class='col-4 strong'>
                  <label for='sid'>User ID: </label>
                </div>
                <div class='col-8'>
                  <input type='text' name='sid' placeholder='Student/Staff ID...' required>
                </div>
              </div>
              <div class='row mb-1'>
                <div class='col-4 strong'>
                  <label for='password'>Password: </label>
                </div>
                <div class='col-8'>
                  <input type='password' name='password' placeholder='Your password...' required>
                </div>
              </div>
              <div class='row mb-1'>
                <div class='col-12'>
                  <button type='submit' id='login' class='btn-info'>Login</button>
                </div>
              </div>
              </form>
              </div>";

        } else {

          //for authenticated users
          echo"<div id='rechargeForm col-12'>
                <div class='row'>
                  <div class='col-12 mb-3 strong whiteTitle'>
                    <center>Welcome back ".$_SESSION['name']." !</center>
                  </div>
                  <div class='col-12 mb-1 strong'>
                    <span>Your balance is : $".$_SESSION['balance']."</span>
                  </div>
                  <div class='col-12 mb-1'>
                    <a href='./account.php'><button type='button' class='btn-info'>&#9786; My account</button></a>
                  </div>
                  <div class='col-12 mb-1'>
                    <a href='./logout.php'><button type='button' class='btn-alert'>&#9888; Logout</button></a>
                  </div>
                </div>
              </div>";
        };
      ?>
      </div>
    </div>
  </div>
</div>

<?php include('./footer.php') ?>