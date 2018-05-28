<?php
//============================================================
//get DBsettings
$dbfile = fopen('./db.txt', 'r');
while(!feof($dbfile)){
	$dbsettings[] = trim(fgets($dbfile));
};

//connect DB
$conn = new mysqli($dbsettings[0], $dbsettings[1], $dbsettings[2], $dbsettings[3]);
if($conn->connect_error){
	$error = $conn->connect_errno.": ".$conn->connet_error;
};

//============================================================
?>