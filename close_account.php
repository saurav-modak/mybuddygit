<?php
include("includes/header.php");

if(isset($_POST['cancel'])){
    header("Location: settings.php");
}
if(isset($_POST['close_account'])){
    $close_query = mysqli_query($con,"UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
    session_destroy();
    header("Location: register.php");

}
unset($_POST);
//echo "<pre>"; print_r($_POST) ;  echo "</pre>";
/*Array
(
    [close_account] => close_account
)
*/
?>
<div class="main_column column">
<h1 align="center"> Close Account</h1>
Are you sure you want to close your account?<br></br>
Clossing your account will hide your profile and all your acitivity from other users.<br><br>
You can re-open your account at any time just by simply logging in.<br><br>

<form action="close_account.php" method="POST">
    <input type="submit" name="close_account" id="close_account" value="Yes! Close it!">
    <input type="submit" name="cancel" id="cancel" value="No! I changed my mind!">
</form>


