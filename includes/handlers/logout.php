<?php

//####################################### Set Online_status to off ##########################################

require '../../config/config.php';

$userLoggedIn=$_SESSION['username'];
$userid_a=mysqli_fetch_array(mysqli_query($con,"SELECT id FROM users WHERE username='$userLoggedIn'"));
$userid=$userid_a['id'];
$ifexist_q=mysqli_query($con,"SELECT * FROM login_status WHERE userid='$userid'");

if(mysqli_num_rows($ifexist_q)){
    //update last_login time 
    $upate_status_query = mysqli_query($con,"UPDATE login_status SET logged_out='yes' WHERE userid='$userid'");
}

//###########################################################################################################

session_destroy();
header("location:../../register.php");


?>