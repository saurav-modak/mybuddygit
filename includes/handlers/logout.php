<?php

//####################################### Set Online_status to off ##########################################

require '../../config/config.php';

$userLoggedIn=$_SESSION['username'];
$userid=mysqli_fetch_array(mysqli_query($con,"SELECT id FROM users WHERE username='$userLoggedIn'"));
$ifexist_q=mysqli_query($con,"SELECT * FROM login_status WHERE userid='$userid[0]'");

if(mysqli_num_rows($ifexist_q)){
    //update last_login time 
    $upate_status_query = mysqli_query($con,"UPDATE login_status SET online_status='off' WHERE userid='$userid[0]'");
}

//###########################################################################################################

session_destroy();
header("location:../../register.php");


?>