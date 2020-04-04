<?php
include("includes/header.php");
include("includes/handlers/settings_handler.php");
?>

<div class="column">
    <div class="center_div">
    <h4>Account Settings</h4>
    <?php
        //header already have following two lines but we still had to use those again because otherwise we have to refresh the page evey time we update user details in order to show the updated values
        $user_details_query2=mysqli_query($con,"SELECT *  FROM users WHERE username='$userLoggedIn'");
        $user2=mysqli_fetch_array($user_details_query2);
   
        echo "<img src=" .$user2['profile_pic']." id='small_profile_pics'>";
    ?>
    <br>
    <a href="upload.php"> Upload New Profile Picture</a><br><br>
    
    <h6>Modify the values and click 'Update Details'</h6><br>
    <form action="settings.php" method="POST">
        <table>
            <tr>
                <td>First Name : </td>
                <td><input type="text" name="first_name" value="<?php echo $user2['first_name']; ?> "></td>
            </tr>

            <tr>
                <td>Last Name : </td>
                <td><input type="text" name="last_name" value="<?php echo $user2['last_name']; ?> "></td>
            </tr>

            <tr>
                <td> User Email : </td>
                <td><input type="text" name="email" value="<?php echo $user2['email']; ?> "></td>
            </tr>

            <tr>
                <td></td>
                <td><input type="submit" name="update_details" id="save_details" value="Update Details"></td>
            </tr>
        </table>
        <b><?php echo $message; ?></b>
    </form>

    <h6>Change Password</h6><br>
    <form action="settings.php" method="POST">
        <table>
            <tr><td>Old Password:</td><td><input type="password" name="old_password"></td></tr>
            <tr><td>New Password:</td><td><input type="password" name="new_password_1"></td></tr>
            <tr><td>Confirm Paaword:</td><td><input type="Password" name="new_password_2"></td></tr>
            <tr><td></td><td><input type="submit" name="update_passwrod" id="change_paasword" value="Change Password"></td></tr>
        </table>
        <b><?php echo $passwrod_message; ?></b>
    </form>
    

    <h6>Close Account</h6>
    <form action="close_account.php" method="POST">
        <input type="submit" name="confirm_close_account" id="confirm_close_account" value="Close Account">
    </form>
    </div>
</div>