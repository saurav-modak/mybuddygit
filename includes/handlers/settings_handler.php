<?php
if(isset($_POST['update_details'])){
    $first_name= $_POST['first_name'];
    $last_name= $_POST['last_name'];
    $email= $_POST['email'];

    $email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    $row=mysqli_fetch_array($email_check);
    $matched_user = $row['username'];
    
    if($matched_user == NULL || $matched_user==$userLoggedIn){
        $update_names_query=mysqli_query($con,"UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
        $message="Details Updated!<br><br>";

    }
    else
        $message ="That email is allready in use <br><br>";
}else
    $message=NULL;

//*******************************************************************************************

if(isset($_POST['update_passwrod'])){
    $old_password = $_POST['old_password'];
    $new_password_1=$_POST['new_password_1'];
    $new_password_2=$_POST['new_password_2'];

    $password_query=mysqli_query($con,"SELECT password FROM users WHERE username='$userLoggedIn'");
    $row=mysqli_fetch_array($password_query);
    $current_password=$row['password'];

    if(md5($old_password) == $current_password){
        //echo "stage1";
        if($new_password_1==$new_password_2){
            //echo "stage2";
            /*check_password length srength etc in future

            code:

            */
            $password=md5($new_password_2);
            $update_password=mysqli_query($con,"UPDATE users SET password='$password' WHERE username='$userLoggedIn'");
            $passwrod_message="Password Updated<br>";
        }else{
            $passwrod_message ="Your two password did not match!<br>";
            //echo "stage3";
        }
    }else{
        $passwrod_message ="Your old password did not match!<br>";
        //echo "stage4";
    }

}else{
    $passwrod_message =NULL;
    //echo "stage5";
}

if(isset($_POST['close_account'])){
    header("Location: close_account.php");
}

?>