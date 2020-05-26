<?php
if(isset($_POST['sendcode'])){
    $email=filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL);
    
    $_SESSION['log_email']=$email;//Store email into session variable

    $check_database_query = mysqli_query($con,"SELECT * FROM users WHERE email='$email'");
    $check_login_query = mysqli_num_rows($check_database_query);

    function random_strings($length_of_string) 
    { 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
        return substr(str_shuffle($str_result),0, $length_of_string); 
    }

    if($check_login_query==1){
        $row = mysqli_fetch_array($check_database_query);
        $key = random_strings(8);
        $add_hash=mysqli_query($con,"UPDATE users SET req_recovery='1', rec_key='$key' where email='$email'");
        
        require_once "includes/classes/sendmail.php";
        sendcode($key,$email);
        

        /*header("location:veryfy_recovery.php");//redirect user to the index page
        exit();*/
    }else{
        array_push($error_array,"Email id not found<br>");
    }

}



if(isset($_POST['veryfy_code'])){
    $email=filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL);
    $newpass=$_POST['new_password'];
    $newpass=md5($newpass);
    $veryfy_code=$_POST['key'];

    $check_database_query = mysqli_query($con,"SELECT * FROM users WHERE email='$email' && rec_key='$veryfy_code'");
    $check_login_query = mysqli_num_rows($check_database_query);

    if($check_login_query==1){
        $row = mysqli_fetch_array($check_database_query);
        $add_hash=mysqli_query($con,"UPDATE users SET req_recovery='0', rec_key='none', password='$newpass' where email='$email'");
        
        unset($_SESSION['codesent']);
        $_SESSION['passchanged']="yes";

        /*header("location:veryfy_recovery.php");//redirect user to the index page
        exit();*/
    }else{
        array_push($error_array,"OTP did not match!<br>");
    }

}

?>