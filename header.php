<!--Header for the every pages-->
<?php 
//============================================================
//session start
  session_start();

//============================================================  

//define timezone, get current time, next working day and date
date_default_timezone_set('Australia/Melbourne');
$weekday = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
if(date('w')<5){
  $nextDay = date('w')+1;
  $orderdate = date('Y-m-d', strtotime('+1 day'));
} else {
  $nextDay = 1;
  if(date('w')>5){
    $orderdate = date('Y-m-d', strtotime('+2 day'));
  } else {
    $orderdate = date('Y-m-d', strtotime('+3 day'));
  };
};
$currentTime = date('H:i');

//============================================================
//DB
  include('./conn.php');

//============================================================  
?>

<!doctype html>
<html lang="en">
  <head>
    <link rel='stylesheet' href='./css/mystyle.css'>
    <!--Viewport for responsive design-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='https://code.jquery.com/jquery-3.3.1.min.js'></script>
    <!--master jquery for menu bar and responsive design-->
    <script src='./js/jquery.js'></script>
    <title>Y.E.O.M. Pty. Ltd.</title>
  </head>
  <body>
