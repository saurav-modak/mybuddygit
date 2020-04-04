<?php

session_start();//this creates a season variable to keep already entered data in form
/*
    //se contents of the array for debugging purpose
    print_r($_SESSION);
*/

$timezone = date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)

//Database connection variables
$server="localhost";
$user ="root";
$pass ="";
$db = "mybuddy";
$con = mysqli_connect($server,$user,$pass,$db);
if($con){
	/*echo "<!--Connected!!-->";*/
}else{
    die("connction failed because: ".mysqli_connect_error());
}


?>

