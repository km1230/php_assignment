<div id='menu'>
  <nav>
    <a href='./index.php' id='logo'><img src='./img/cafe.jpg' alt='Site Logo'></a>
    <a href='./index.php' class='nav-link' id='home'>Home</a>
    <div class='nav-item'>
      <a href='./lazenbys.php' class='nav-link' id='lazenbys'>Lazenbys</a>
      <div class='dropdown'>
        <?php 
          if(preg_match('/1$/', $_SESSION['permission']) || $_SESSION['permission']==400){
            echo "<a href='./lazenbys_order.php'>View Orders</a>";
          };
          if(preg_match('/301/', $_SESSION['permission']) || $_SESSION['permission']==400){
            echo "<a href='./lazenbys_manager.php'>Manager</a>";
          };
          if($_SESSION['permission']==400){
            echo "<a href='./lazenbys_master.php'>Master</a>";
          };
        ?>
      </div>
    </div>
    <div class='nav-item'>
      <a href='./ref.php' class='nav-link' id='ref'>The Ref</a>
      <div class='dropdown'>
        <?php 
          if(preg_match('/2$/', $_SESSION['permission']) || $_SESSION['permission']==400){
            echo "<a href='./ref_order.php'>View Orders</a>";
          };
          if(preg_match('/302/', $_SESSION['permission']) || $_SESSION['permission']==400){
            echo "<a href='./ref_manager.php'>Manager</a>";
          };
          if($_SESSION['permission']==400){
            echo "<a href='./ref_master.php'>Master</a>";
          };
        ?>
      </div>
    </div>
    <div class='nav-item'>
      <a href='./trade.php' class='nav-link' id='trade'>Trade Table</a>
      <div class='dropdown'>
        <?php 
          if(preg_match('/3$/', $_SESSION['permission']) || $_SESSION['permission']==400){
            echo "<a href='./trade_order.php'>View Orders</a>";
          };
          if(preg_match('/303/', $_SESSION['permission']) || $_SESSION['permission']==400){
            echo "<a href='./trade_manager.php'>Manager</a>";
          };
          if($_SESSION['permission']==400){
            echo "<a href='./trade_master.php'>Master</a>";
          };
        ?>
      </div>
    </div>

    <?php
      //Display MyAccount & MyCart to authenticated users
      if(isset($_SESSION['sid'])){
        echo"<a href='./account.php' class='nav-link' id='account'>MyAccount</a>
        <a href='./cart.php' class='nav-link' id='cart'>MyCart</a>";
      };
      //Display Allocate to director
      if($_SESSION['permission']==400){
      	echo"<a href='./allocate.php' class='nav-link' id='allocate'>Allocate</a>";
      };
    ?>

    <!--SignUp / Logout button-->
    <?php
      //Display SignUp button to unauthenticated visitors
      if(!isset($_SESSION['sid'])){
        echo "<a href='./registration.php' class='nav-link menu-right' id='reg'>Sign Up!</a>";
      } else {
        //Display Logout button to authenticated users
        echo "<a href='./logout.php' class='nav-link menu-right' id='logout'>Logout</a>";
      }
    ?>
   </nav>
 
 <!--Burger button for responsive design on mobile devices-->  
  <div>
    <a href='#' id='menuToggle'>&#9776;</a>
  </div>
  </div>

<div class='rowspace'></div>