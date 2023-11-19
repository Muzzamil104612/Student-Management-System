<?php

$host="127.0.0.1:3306";
$username="root";
$password="123456";
$db="lms";

$conn=mysqli_connect($host,$username,$password,$db);

if(!$conn){
	die("Connection Failed ". mysqli_connect_error());
}
else {
	//echo "Connection to db = ".$db ." is successful";

	
}










?>