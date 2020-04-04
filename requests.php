<?php
    include("includes/header.php");
?>
<div class="main_column column" id="main_column">
    <div class="friend_requests">
    <h4>Friend Requests</h4>

    <?php

        $query=mysqli_query($con,"SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
        if(mysqli_num_rows($query) == 0)
            echo "You have no friend requests currently";
        else{
            while($row=mysqli_fetch_array($query)){
                $user_from=$row['user_from'];
                $user_from_obj = new User($con, $user_from);

                echo "<div id='friend_req_list'><a href='$user_from'> <img src=". $user_from_obj -> getProfilePic().">";
                echo "<div class='texts' <b>". $user_from_obj -> getFirstAndLastName()."</b></a> sent you a friend request! </div>";

                $user_from_friend_array = $user_from_obj -> getFriendArray();
                
                /* OLD FRIEND REQUEST
                    if(isset($_POST['accept_request' . $user_from])){ //if($_POST['accept_request']==$user_from) and set he value="Accept" to value="$user_from"
                        $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array,'$user_from,') WHERE username='$userLoggedIn'");
                        $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array,'$userLoggedIn,') WHERE username='$user_from'");

                        $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn'AND user_from='$user_from'");
                        
                        echo "You are now friends!";
                        header("Location: requests.php");
                    }   
                    if(isset($_POST['ignore_request' . $user_from])){
                        $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn'AND user_from='$user_from'");
                        echo "Request ignored";
                        header("Location: requests.php");
                    }
                    ?>
                    <form action="requests.php" method="POST">
                        <input type="submit" name="accept_request<?php echo $user_from ?>" id="accept_button" value="Accept">
                        <input type="submit" name="ignore_request<?php echo $user_from ?>" id="ignore_button" value="Ignore">
                    </form>


                    
                    <?php
                */
                //BETTER FRIEND REQUEST
                
                if(isset($_POST[$user_from]) AND $_POST[$user_from]=='Accept'){
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array,'$user_from,') WHERE username='$userLoggedIn'");
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array,'$userLoggedIn,') WHERE username='$user_from'");

                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn'AND user_from='$user_from'");
                    
                    echo "You are now friends!";
                    header("Location: requests.php");
                }   
                if(isset($_POST[$user_from]) AND $_POST[$user_from]=='Ignore'){
                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn'AND user_from='$user_from'");
                    echo "Request ignored";
                    header("Location: requests.php");
                }
                ?>
                <form action="requests.php" method="POST">
                    <input type="submit" name="<?php echo $user_from ?>" id="accept_button" value="Accept">
                    <input type="submit" name="<?php echo $user_from ?>" id="ignore_button" value="Ignore">
                </form>
                </div>
                <?php
                
            }
        }
    ?>
    </div>

</div>