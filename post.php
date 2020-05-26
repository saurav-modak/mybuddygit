<?php
include("includes/header.php");

if(isset($_GET['id'])){
    $id=$_GET['id'];
}else{
    $id=0;
}

?>
<div class="left_side_bar">
        <div class="user_details column">
            <a href="upload.php">
            <img src="<?php echo $user['profile_pic']; ?>">
        </a>
            <div class="user_details_left_right">
                <a href="<?php echo $userLoggedIn ?>">
                    <?php echo $user['first_name']." ".$user['last_name'];?>
                </a>
                <br>
                <?php
                    echo "Posts: ".$user['num_posts']."<br>";
                    echo "Likes: ".$user['num_likes'];
                ?>
            </div>
        </div>
        <?php include("online_friends.php"); ?>
    </div>
    <div class="main_column column" id="main_column">
        <?php
            $post =new Post($con,$userLoggedIn);
            $post -> getSinglePost($id);
        ?>
    </div>