
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <?php
        require 'config/config.php';
        include("includes/classes/User.php");
        include("includes/classes/Post.php");
        if(isset($_SESSION['username'])){
            $userLoggedIn=$_SESSION['username'];
            $user_details_query=mysqli_query($con,"SELECT *  FROM users WHERE username='$userLoggedIn'");
            $user=mysqli_fetch_array($user_details_query);
        }else{
            header("location: register.php");
        }
    ?>
    <script>
        function toggle(){
            var element = document.getElementById("comment_section");
            
            if(element.style.display == "block")
                element.style.display = "none";
            else
                element.style.display = "block";
        }
    </script>

    <!-- Posting comments -->
    <?php
        //get id of post... it wil be sent as parameter using url get
        if(isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
            
        }
        $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($user_query);

        $added_by =  $row['added_by'];//????
        $user_to =  $row['user_to'];//????

        if(isset($_POST['postComment'. $post_id])){
            $post_body =$_POST['post_body'];
            $post_body =mysqli_escape_string($con, $post_body);
            $date_time_now = date("Y-m-d H:i:s");
             $comment_hash=md5($post_body);

             $nospace =  trim(str_replace(array("\\n", "\\r"), NULL, $post_body));//working

            /*
             still dont understand why preg_replace('/[\r\n]+/','',$post_body) did not worked
             $nospace =  explode("\n", str_replace("\r", "", $post_body));
             $remove=array('/\t/','/\v/');
             $replace='';
             $nospace=preg_replace($remove,$replace,$post_body);
            */
             //echo "<script>alert(',$nospace,')</script>";

             
            require 'includes/classes/Notification.php';
            if(!empty($nospace)){//if some value left after triming than it is not empty
                $insert_post = mysqli_query($con,"INSERT INTO comments VALUES (NULL,'$post_body','$userLoggedIn','$added_by','$date_time_now','no','$post_id','$comment_hash')" );
                
                //insert notification
                if($added_by  != $userLoggedIn){
                    //if commentor not the owner
                    //owner receives notificaton
                    
                    $notification = new Notification($con,$userLoggedIn);
                    $notification->insertNotification($post_id,$added_by,"comment");
                }

                if($user_to != 'none' && $user_to != $userLoggedIn){
                    //if post is dedicated to A
                    // and commentiner is not A
                    //(when someone comment on your wall post
                    //you will receive a notification)
                    $notification = new Notification($con,$userLoggedIn);
                    $notification->insertNotification($post_id,$added_by,"comment");
                }
                
                //when someone comment on a post you commented eveyone willbe notified
                $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
                $notified_users = array();
                while($row=mysqli_fetch_array($get_commenters)){
                    if($row['posted_by'] != $added_by && $row['posted_by'] != $user_to
                        && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'],$notified_users)){
                        //if comment posted_by is not same as who ($posted_to) posted the comment
                        //and  comment posted_by is not same as the ($user_to) ownder of the post.
                        //if comment posted by in ot the user commenting
                            
                        $notification = new Notification($con,$userLoggedIn);
                        $notification->insertNotification($post_id,$row['posted_by'],"comment_non_owner");
                        
                        array_push($notified_users,$row['posted_by']);
                    }
                    
                }

            }else{
                echo "<script>alert('Cannot post blank comment!')</script>";
            }
        }

    ?>
    
    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postCommnet<?php echo $post_id; ?>" method="POST">
        <textarea name="post_body"></textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
    </form>

    <!-- Load Comments -->
    <?php
    $get_comments = mysqli_query($con,"SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id DESC");
    $count = mysqli_num_rows($get_comments);

    if($count != 0){
        while($comment = mysqli_fetch_array($get_comments)){
            $comment_body = $comment['post_body'];
            $posted_by = $comment['posted_by'];
            $added_by = $comment['posted_to'];
            $date_added = $comment['date_added'];
            $remove = $comment['remove'];

            //time msg
                $date_time_now = date("Y-m-d H:i:s");
                $start_date= new DateTime($date_added);//Time of post
                $end_date= new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date);

                if($interval->y>=1){
                    if($interval==1)
                        $time_message=$interval->y." Year ago";//1 year ago
                    else
                        $time_message=$interval->y." Years ago";//1+ year ago
                }

                else if($interval->m >= 1){
                    if($interval->d==0){
                        $days=" ago";
                    }
                    else if($interval -> d ==1){
                        $days=$interval->d." Day ago";
                    }
                    else{
                        $days=$interval->d." Days ago";
                    }

                    if($interval->m==1){
                        $time_message=$interval->m." Moth".$days;
                    }
                    else{
                        $time_message=$interval->m." Moths".$days; 
                    }
                }

                else if($interval->d>=1){
                    if($interval -> d ==1){
                        $time_message="Yesterday";
                    }
                    else{
                        $time_message=$interval->d." Days ago";
                    }
                }

                else if($interval->h>=1){
                    if($interval->h==1){
                        $time_message=$interval->h." Hour ago";
                    }
                    else{
                        $time_message=$interval->h." Hours ago";
                    }
                }

                else if($interval->i>=1){
                    if($interval->i==1){
                        $time_message=$interval->i." Minute ago";
                    }
                    else{
                        $time_message=$interval->i." Minutes ago";
                    }
                }

                else{
                    if($interval->s<30){
                        $time_message=" Just now";
                    }
                    else{
                        $time_message=$interval->s." Seconds ago";
                    }
                }
        $user_obj = new User($con, $posted_by);
        ?>
            <div class="comment_section">
                <!-- target="_parent" open iframe links outside the iframe -->
                <a href="<?php echo $posted_by; ?>" target="_parent">
                    <img src="<?php echo $user_obj -> getProfilePic() ?>" title="<?php echo $posted_by; ?>" style="float:left" height="30">
                    <b><?php echo $user_obj-> getFirstAndLastName();?></b>    
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message."<br>".$comment_body; ?>
                <hr>
            </div>

        <?php
        }
    }
    else{
        echo "<center><br>No Comments to Show! </center>";
    }
    ?>

    

</body>
</html>