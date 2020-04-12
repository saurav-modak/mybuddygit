<?php

//#################################### Online users last acivity time ########################################

//echo "<script> alert('".$user[0]."')</script>";
$userid = $user[0];
$ifexist_q= mysqli_query($con,"SELECT * FROM login_status WHERE userid='$userid'");

if(mysqli_num_rows($ifexist_q)){
    //data update for existing users
    $upate_status_query = mysqli_query($con,"UPDATE login_status SET last_activity=CURTIME(), online_status='on' WHERE userid='$userid'");
}else{
    //table data insertion for new users
    $upate_status_query = mysqli_query($con,"INSERT INTO login_status VALUES (NULL,'$userid',CURTIME(),CURTIME(),'on')");
}


?>
