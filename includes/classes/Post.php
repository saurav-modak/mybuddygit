<?php

class Post {
    private $user_obj;
    private $con;

    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);//calling User.php class for use info
        
    }
    
    public function submitPost($body, $user_to, $imageName) {
        
        $body = strip_tags($body); //removes html tags 
        $body = mysqli_real_escape_string($this->con, $body);
        $check_empty = preg_replace('/\s+/', '', $body);//delets all spaces
       // echo "<script>alert('Alert $imageName')</script>";

        if($check_empty !=="" || $imageName!==NULL){//either there is text and image,or jus text or just image, in the post

            /*//        Yotube link posting
            //!         Buggy on linux 

            $body_array = preg_split("/\s+/",$body);
            foreach($body_array as $key => $value){
                if(strpos($value, "www.youtube.com/watch?v=") !== false ){

                    $value = preg_replace("!watch\?v=!","embed/", $value);
                    $value = "<br><iframe width=\'420\' height=\'235\' src=\'".$value."\'></iframe><br>";
                    $body_array[$key] = $value;
                }
            }

            $body = implode(" ",$body_array);
            */

            // Found solution at https://github.com/christianh814/bumper/blob/master/includes/classes/Post.php
       
            $body_array = preg_split("/\s+/", $body);// !Issue: not splitting newlines
			foreach($body_array as $key => $value) {
				if (strpos($value, "www.youtube.com/watch?v=") !== false){
					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/" , $link[0]);
					$value = "<br><iframe width=\'80%\' height=\'350px\' src=\'" . $value . "\'></iframe><br>";
					$body_array[$key] = $value;
				} elseif (strpos($value, "youtu.be/") !== false) {
					$link = preg_split("!\.be/!", $value);
					$value = "<br><iframe width=\'420\' height=\'235\' src=\'https://www.youtube.com/embed/" . $link[1] . "\'></iframe><br>";
					$body_array[$key] = $value;
				}
			}
			$body = implode(" ", $body_array);




            //current date and time
            $date_added = date("Y-m-d H:i:s");
            //get username
            $added_by = $this->user_obj->getUsername();

            //if user in not on own profile, user_to is 'none'
            if($user_to == $added_by){
                $user_to = "none";
            }
            
            $post_hash=md5($body);
            $duplicate_post_query=mysqli_query($this->con,"SELECT date_added FROM posts WHERE post_hash='$post_hash' ORDER BY id DESC LIMIT 1");
            $duplicate_array=mysqli_fetch_assoc($duplicate_post_query);

            $duplicate_post=0;//false to avoid undefined variable in case not duplicate post so ther is no data in query 
            if($duplicate_array){
                $date_added1=$duplicate_array['date_added'];//2020-03-01 04:21:50
                $data_type_to_compare=gettype($date_added1);//string
                $int_date=strtotime($date_added1);//1583016710
                $int_data_type=gettype($int_date);//integer
                $current_time=date("Y-m-d H:i:s");//2020-03-01 04:22:28
                $current_time_data_type=gettype($current_time);//string
                $int_current_time=strtotime($current_time);//1583016748
                $int_current_data_type=gettype($int_current_time);//integer
                $diffrenece= $int_current_time - $int_date;//38
                $waiting_time=300-$diffrenece;
                
                if($diffrenece>0 && $diffrenece<300){
                    $duplicate_post=1;//true
                }
                
                /*
                ! This is duplicate post worning which is also executing when we are clicking on delete post. so disabled it

                if($duplicate_post && isset($_POST['post_text'])){
                    $check = $_POST['post_text'];
                    //echo "<script>alert('Date added is: $date_added1 and data type is: $data_type_to_compare <br> int_date: $int_date data type: $int_data_type <br> current time: $current_time data type: $current_time_data_type current int time:$int_current_time data type: $int_current_data_type Diffrence in seconds: $diffrenece'); </script>";
                    echo "<script>alert('Duplicate post detected, to avoid spamming we allow duplicate post evey 5 minuts wait for $waiting_time seconds more Check is: $check')</script>";
                }*/
            }
            
            if(!$duplicate_post){
                
                //insert post
                //$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added','no','no','0')");//SQL does not accept '' as NULL
                $query = mysqli_query($this->con, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added','no','no','0','$post_hash','$imageName')");
                $returned_id = mysqli_insert_id($this->con);//this function returnes the post id inserted in db
                
                
                //insert notification
                if($user_to != "none"){
                    $userLoggedIn = $this -> user_obj->getUsername();
                    require 'Notification.php';
                    $notification = new Notification($this->con,$userLoggedIn);
                    $notification->insertNotification($returned_id,$user_to,"profile_post");
                }

                //update post count for user
                $num_posts = $this->user_obj->getNumPosts();
                $num_posts++;
                $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
            }
        }
    }      
    
    public function loadPostsFriends($data, $limit){

        $page = $data['page'];
        $userLoggedIn = $this -> user_obj->getUsername();

       
        if($page == 1)
            $start = 0;
        else
            $start = ($page -1 ) * $limit;
            

        $str ="";//string to return

        $data_query = mysqli_query($this->con,"SELECT * FROM posts  WHERE deleted='no' ORDER BY id DESC");//Very inefficient because currently it is loading all posts at once
        //
        //TODO we must use
        //! mysqli_query($this->con,"SELECT * FROM posts  WHERE deleted='no' ORDER BY id DESC LIMI $Start $limit);
        // But doing this will will only load 10 posts and noMorePosts variable will be set to TRUE which will preven any more posts from loading
        // So we need to change ajax_load_posts.php index.php Post.php for that so currently i am leaving this as it is
        //TODO But to improve speed i must follow above
        //TODO PossibleSolution :https://www.youtube.com/watch?v=XRAlEbVL8vQ

        if(mysqli_num_rows($data_query) > 0){

            $num_iterations = 0; //Number of results checked 
            $count = 1;

            
            while($row=mysqli_fetch_array($data_query)){
                $id=$row['id'];
                $body=$row['body'];
                $added_by=$row['added_by'];
                $date_time=$row['date_added'];
                $imagePath= $row['image'];

                //Prepare user_to string so it can be included even if not posted to a user
                if($row['user_to']=="none"){
                    $user_to = "";
                }
                else{
                    $user_to_obj = new User($this->con,$row['user_to']);//creating User object for your friend who posted it
                    $user_to_name = $user_to_obj -> getFirstAndLastName();
                    $user_to = "To <a href='".$user_to_obj -> getUsername()."'>". $user_to_name ."</a>";
                }

                if($row['user_to']!="none" AND $user_to_obj->isClosed()){
                    continue;
                }

                //Check if user who posted, has theri account closed
                $added_by_obj = new User($this->con, $added_by);
                if($added_by_obj -> isClosed()){//isClosed User.php function returns true or false
                    continue; //if friend account is closed this itteration of while loop is skiped
                }

                $user_logged_obj = new User($this->con,$userLoggedIn);
                if($user_logged_obj -> isFriend($added_by)){//if friend or self
                    ////temorary all user posts are visible to each other make second recturn to false to turn it off on inside isFriend() function
                    

                    if($num_iterations++ < $start) // $start is form which number posts needed to be loaded
                        continue;                   // if $num_iteration is less then $start thats means it is already loaded so skip it
                    
                    //Once 10 posts have been loaded, break
                    // Count=1 limit=10
                    if($count > $limit){  // True if $count=11 > $limit=10 
                        break;            // It breaks the loap
                    }else{
                        $count++; //counts number of iterations run
                    }

                    if($userLoggedIn==$added_by)
                        $delete_button = "<button class='delete_button btn-danger' id='post$id'><div class='delete_text'>X</div></button>";
                    else
                        $delete_button ="";

                    $user_details_query = mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                    $user_row =mysqli_fetch_array($user_details_query); 
                    $first_name =$user_row['first_name'];
                    $last_name =$user_row['last_name'];
                    $profile_pic =$user_row['profile_pic'];


                    /*
                    ! this part is for comments, it gives evey post a id to click
                    TODO: maybe i can make this function take post id as a parameter instead of making new function for every post.
                    */
                    ?>
                    <script>
                        function toggle<?php echo $id; ?>(){
                            <?php //to prevent comments from loading when clicked on user name or picture ?>
                            var target = $(event.target);
                            if(!target.is("a")){ <?php //if target is not a hyperlink ?>
                                var element = document.getElementById("toggleComment<?php echo $id; ?>")
                                
                                if(element.style.display == "block")
                                    element.style.display = "none";
                                else
                                    element.style.display = "block";

                                <?php //next two line set iframe height to its content height ?>    
                                var iframe = document.getElementById('comment_iframe<?php echo $id; ?>');
                                iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
    
                            }
                        }
                    </script>
                    <?php

                    //Number of comments or total comments on given post id
                    $comments_check = mysqli_query($this->con ,"SELECT *  FROM comments WHERE post_id='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);

                    //Timeframe
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date= new DateTime($date_time);//Time of post
                    /*
                        TODO: DateTime is a class comes with php
                        ! Learn about it */
                    $end_date= new DateTime($date_time_now); //Current time

                    $interval = $start_date->diff($end_date);
                    /*
                        TODO: diff is a class comes with php
                        ! Learn about it */
                    //time msg
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

                    if($imagePath != NULL){
                        $imageDiv ="<div class='postedImage'>
                                        <img src='$imagePath'><br>
                                    </div>";
                    }else{
                        $imageDiv=NULL;
                    }

                    $str .="<div class='status_post main-column column' onClick='javascript:toggle$id()'>
                                <div class='post_profile_pic'>
                                    <img src='$profile_pic' width='50'>
                                </div>
                                <div class='posted_by' style='color:#ACACAC;'>
                                    <a href='$added_by'>$first_name $last_name </a>
                                    $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                    $delete_button
                                </div>
                                <div id='post_body'>
                                    $body      
                                    <br>
                                    $imageDiv
                                    
                                </div>
                                <div class='newsfeedPostOptions'>
                                    Comments( $comments_check_num )&nbsp&nbsp&nbsp&nbsp
                                    <iframe src='like.php?post_id=$id' class='like_frame' scrolling='no'></iframe>
                                </div>
                            </div>
                                <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                    <iframe src='comment_frame.php?post_id=$id' class='comment_iframe' id='comment_iframe$id' frameborder='0'></iframe>
                                </div>";
                            /*<hr>
                         post id to loead posts are sent through this line as a GET request
                            //!<iframe src='comment_frame.php?post_id=$id>
                            to load comments against the post id */

                }
                ?>
                <script>
                    $(document).ready(function(){
                        $('#post<?php echo $id; ?>').on('click',function(){
                            bootbox.confirm("Are you sure you want to delete this post",function(result){
                                $.post("includes/handlers/delete_post.php?delete_post_id=<?php echo $id; ?>",{result:result});
                                if(result)
                                    location.reload();
                            });
                        })
                    });
                </script>
                <?php
            }

            if($count > $limit){
                $str .="<input type='hidden'  class='nextPage' value='".($page+1)."'>
                        <input type='hidden' class='noMorePosts' value='false'>";
                echo "<script>var noMorePosts='false';</script>";
                
            }else{
                $str .="<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center';> No more posts to display </p>";
                echo "<script>var noMorePosts='true';</script>";
            }

        }
        echo $str;

    }

    public function BetterloadPostsFriends($start, $limit){
        $userLoggedIn = $this -> user_obj->getUsername();
        
        $data_query = mysqli_query($this->con,"SELECT * FROM posts ORDER BY id DESC  LIMIT $start, $limit");
            //print_r($sql); //testing
            if(mysqli_num_rows($data_query) > 0){
                $response = "";
    
                while($row=mysqli_fetch_array($data_query)) {
                    $id=$row['id'];
                    $body=$row['body'];
                    $added_by=$row['added_by'];
                    $date_time=$row['date_added'];
                    
                    if($row['user_to']=="none"){
                        $user_to = "";
                    }
                    else{
                        $user_to_obj = new User($this->con,$row['user_to']);//creating User object for your friend who posted it
                        $user_to_name = $user_to_obj -> getFirstAndLastName();
                        $user_to = "To <a href='".$user_to_obj -> getUsername()."'>". $user_to_name ."</a>";
                    }
                    
                    //Check if user who posted, has theri account closed
                    $added_by_obj = new User($this->con, $added_by);
                    if($added_by_obj -> isClosed()){//isClosed User.php function returns true or false
                        continue; //if friend account is closed this itteration of while loop is skiped
                    }

                    $user_logged_obj = new User($this->con,$userLoggedIn);
                    if($user_logged_obj -> isFriend($added_by)){//if friend or self
                    
                        
                        $user_details_query = mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                        $user_row =mysqli_fetch_array($user_details_query); 
                        $first_name =$user_row['first_name'];
                        $last_name =$user_row['last_name'];
                        $profile_pic =$user_row['profile_pic'];
        
                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date= new DateTime($date_time);//Time of post
                        /*
                            TODO: DateTime is a class comes with php
                            ! Learn about it */
                        $end_date= new DateTime($date_time_now); //Current time
        
                        $interval = $start_date->diff($end_date);
                        /*
                            TODO: diff is a class comes with php
                            ! Learn about it */
                        //time msg
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
        
                        $response .="<div class='status_post'>
                                        <div class='post_profile_pic'>
                                            <img src='$profile_pic' width='50'>
                                        </div>
                                        <div class='posted_by' style='color:#ACACAC;'>
                                            <a href='$added_by'>$first_name $last_name </a>
                                            $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                        </div>
                                        <div id='post_body'>
                                            $body
                                            <br>
                                        </div>
                                    </div>
                                    <hr>
                        "; 
                    } //end of if friend loop 
    
    
                }//end of while loop
                
                exit($response);
    
            } else{
            exit("reachedMax");
        }
    }

    public function loadProfilePosts($data, $limit){

        $page = $data['page'];
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this -> user_obj->getUsername();

       
        if($page == 1)
            $start = 0;
        else
            $start = ($page -1 ) * $limit;
            

        $str ="";//string to return

        $data_query = mysqli_query($this->con,"SELECT * FROM posts  WHERE deleted='no'AND ((added_by='$profileUser' AND user_to='none')OR user_to='$profileUser') ORDER BY id DESC");//Very inefficient because currently it is loading all posts at once
        //
        //TODO we must use
        //! mysqli_query($this->con,"SELECT * FROM posts  WHERE deleted='no' ORDER BY id DESC LIMI $Start $limit);
        // But doing this will will only load 10 posts and noMorePosts variable will be set to TRUE which will preven any more posts from loading
        // So we need to change ajax_load_posts.php index.php Post.php for that so currently i am leaving this as it is
        //TODO But to improve speed i must follow above
        //TODO PossibleSolution :https://www.youtube.com/watch?v=XRAlEbVL8vQ

        if(mysqli_num_rows($data_query) > 0){

            $num_iterations = 0; //Number of results checked 
            $count = 1;

            
            while($row=mysqli_fetch_array($data_query)){
                $id=$row['id'];
                $body=$row['body'];
                $added_by=$row['added_by'];
                $date_time=$row['date_added'];

                // !Check if user who posted, has theri account closed ***controvasal***
                $added_by_obj = new User($this->con, $added_by);
                if($added_by_obj -> isClosed()){//isClosed User.php function returns true or false
                    continue; //if friend account is closed this itteration of while loop is skiped
                }

                if($num_iterations++ < $start) // $start is form which number posts needed to be loaded
                    continue;                   // if $num_iteration is less then $start thats means it is already loaded so skip it
                
                //Once 10 posts have been loaded, break
                // Count=1 limit=10
                if($count > $limit){  // True if $count=11 > $limit=10 
                    break;            // It breaks the loap
                }else{
                    $count++; //counts number of iterations run
                }

                if($userLoggedIn==$added_by)
                    $delete_button = "<button class='delete_button btn-danger' id='post$id'><div class='delete_text'>X</div></button>";
                else
                    $delete_button ="";

                $user_details_query = mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                $user_row =mysqli_fetch_array($user_details_query); 
                $first_name =$user_row['first_name'];
                $last_name =$user_row['last_name'];
                $profile_pic =$user_row['profile_pic'];


                /*
                ! this part is for comments, it gives evey post a id to click
                TODO: maybe i can make this function take post id as a parameter instead of making new function for every post.
                */
                ?>
                <script>
                    function toggle<?php echo $id; ?>(){
                        <?php //to prevent comments from loading when clicked on user name or picture ?>
                        var target = $(event.target);
                        if(!target.is("a")){ <?php //if target is not a hyperlink ?>
                            var element = document.getElementById("toggleComment<?php echo $id; ?>")
                            
                            if(element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";

                            <?php //next two line set iframe height to its content height ?>    
                            var iframe = document.getElementById('comment_iframe<?php echo $id; ?>');
                            iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';

                        }
                    }
                </script>
                <?php

                //Number of comments or total comments on given post id
                $comments_check = mysqli_query($this->con ,"SELECT *  FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date= new DateTime($date_time);//Time of post
                /*
                    TODO: DateTime is a class comes with php
                    ! Learn about it */
                $end_date= new DateTime($date_time_now); //Current time

                $interval = $start_date->diff($end_date);
                /*
                    TODO: diff is a class comes with php
                    ! Learn about it */
                //time msg
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

                $str .="<div class='status_post' onClick='javascript:toggle$id()'>
                            <div class='post_profile_pic'>
                                <img src='$profile_pic' width='50'>
                            </div>
                            <div class='posted_by' style='color:#ACACAC;'>
                                <a href='$added_by'>$first_name $last_name </a>
                                &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                $delete_button
                            </div>
                            <div id='post_body'>
                                $body      
                                <br>
                            </div>
                            <div class='newsfeedPostOptions'>
                                Comments( $comments_check_num )&nbsp&nbsp&nbsp&nbsp
                                <iframe src='like.php?post_id=$id' class='like_frame' scrolling='no'></iframe>
                            </div>
                        </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                <iframe src='comment_frame.php?post_id=$id' class='comment_iframe' id='comment_iframe$id' frameborder='0'></iframe>
                            </div>
                        <hr>
                    ";/* post id to loead posts are sent through this line as a GET request
                        //!<iframe src='comment_frame.php?post_id=$id>
                        to load comments against the post id */

            
                ?>
                <script>
                    $(document).ready(function(){
                        $('#post<?php echo $id; ?>').on('click',function(){
                            bootbox.confirm("Are you sure you want to delete this post",function(result){
                                $.post("includes/handlers/delete_post.php?delete_post_id=<?php echo $id; ?>",{result:result});
                                if(result)
                                    location.reload();
                            });
                        })
                    });
                </script>
                <?php
            }

            if($count > $limit){
                $str .="<input type='hidden'  class='nextPage' value='".($page+1)."'>
                        <input type='hidden' class='noMorePosts' value='false'>";
                echo "<script>var noMorePosts='false';</script>";
                
            }else{
                $str .="<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center';> No more posts to display </p>";
                echo "<script>var noMorePosts='true';</script>";
            }

        }
        echo $str;

    }
    

    
}
?>