<?php
if(isset($_POST['login_button'])){
    $email=filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL);
    
    $_SESSION['log_email']=$email;//Store email into session variable
    $password=md5($_POST['log_password']);//get password

    $check_database_query = mysqli_query($con,"SELECT * FROM users WHERE email='$email' AND password='$password'");
    $check_login_query = mysqli_num_rows($check_database_query);

    if($check_login_query==1){
        $row = mysqli_fetch_array($check_database_query);
        $username= $row['username'];

        $user_closed_query=mysqli_query($con,"SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
        if(mysqli_num_rows($user_closed_query)==1){
            $reopen_account=mysqli_query($con,"UPDATE users SET user_closed='no'where email='$email'");
        }

        $_SESSION['username']=$username;//stores username in season variable to idenify logged in user

//########################################## Online users login time ########################################



        //  se if user has previous login data saved in login_status table
        $userid=mysqli_fetch_array(mysqli_query($con,"SELECT id FROM users WHERE username='$username'"));
        $ifexist_q=mysqli_query($con,"SELECT * FROM login_status WHERE userid='$userid[0]'");

        if(mysqli_num_rows($ifexist_q)){
            //update last_login time 
            $upate_status_query = mysqli_query($con,"UPDATE login_status SET last_login=CURTIME() WHERE userid='$userid[0]'");
        }
//############################################################################################################

        header("location:index.php");//redirect user to the index page
        exit();
    }else{
        array_push($error_array,"Email or password was incorrect<br>");
    }

}

?>