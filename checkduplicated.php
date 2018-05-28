<?php
//============================================================
//start session & load header page
session_start();
include('./conn.php');

//============================================================
//check duplicated user id
if(isset($_GET['sid'])){
	$checkSid = $conn->prepare("SELECT sid FROM users WHERE sid = ?");
	$checkSid->bind_param('s', $_GET['sid']);
	$checkSid->execute();
	$checkSid->store_result();
	if($checkSid->num_rows > 0){
		echo "User ID exists already!";
	} else {
		echo "";
	};
	$checkSid->close();
};

//============================================================
//check duplicated email
if(isset($_GET['email'])){
	$checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
	$checkEmail->bind_param('s', $_GET['email']);
	$checkEmail->execute();
	$checkEmail->store_result();
	if($checkEmail->num_rows > 0){
		echo "This email address is used already!";
	} else {
		echo "";
	};
	$checkEmail->close();
};

//============================================================
//check if email duplicated with other users (MyAccount)
if(isset($_GET['updateEmail'])){
	$checkEmail = $conn->prepare("SELECT sid FROM users WHERE email = ?");
	$checkEmail->bind_param('s', $_GET['updateEmail']);
	$checkEmail->execute();
	$checkEmail->store_result();
	if($checkEmail->num_rows > 0){
		$checkEmail->bind_result($sid);
		$checkEmail->fetch();
		if($_SESSION['sid'] != $sid){
			echo "This email address is used already!";
		};
	} else {
		echo "";
	};
	$checkEmail->close();
};

//============================================================
?>