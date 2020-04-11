<?php
class Notification {
    private $user_obj;
    private $con;

    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);//calling User.php class for use info
    }

    public function getUnreadNumber(){
        $userLoggedIn = $this -> user_obj ->getUsername();
        $query = mysqli_query($this->con,"SELECT * FROM notification WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }

    public function getNotifications($data,$limit){
           
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";

        if($page == 1)
            $start = 0;
        else
            $start = ($page-1)*$limit;

        $set_viewed_query = mysqli_query($this -> con, "UPDATE notification SET viewed='yes' WHERE user_to='$userLoggedIn'");

        $query = mysqli_query($this->con,"SELECT * FROM notification WHERE user_to='$userLoggedIn' ORDER BY id DESC");       
        
        if(mysqli_num_rows($query)==0){
            echo "You have no notification!";
        }

        $num_iterations = 0; // Number of messages checked
        $count  = 1; //Number of messages posted
        while($row=mysqli_fetch_array($query)){

            if($num_iterations++ < $start)
                continue;
            if($count> $limit)
                break;
            else
                $count++;
            
            $user_from = $row['user_from'];
            $user_data_query = mysqli_query($this->con,"SELECT * FROM users WHERE username='$user_from'");
            $user_data = mysqli_fetch_array($user_data_query);

            //Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date= new DateTime($row['datetime']);//Time of post
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


            $opned = $row['opened'];
            $style=($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";


            $return_string .= "<div class='single_notification'>
                <a href='".$row['link']."'>
                <div class='notificationsProfilePic'>
                    <img src='".$user_data['profile_pic']."'>
                </div>
                <div class='timestamp_smaller'>
                    ".$time_message."
                </div>
                <div class='notification_msg'>
                    ".$row['message']."
                </div>  
                </a>
            </div>
            <div class='notification_hr'><hr></div>
            "; 
        }
        //If posts were loaded
        if($count > $limit){
            $return_string .= "<input type='hidden' class='nextPageDropDownData'value='".($page+1)."'>
            <input type='hidden' class='noMoreDorpDownData' value='false'>";
            }else{
            $return_string .="<input type='hidden' class='noMoreDorpDownData' value='true'>
            <p style='text-align:center;>No more notification to load!</p>";
        }  
        return $return_string.="";
    }

    public function insertNotification($post_id, $user_to, $type){
            $userLoggedIn = $this->user_obj->getUsername();
            $userLoggedInName = $this -> user_obj -> getFirstAndLastName();

            $date_time = date("Y-m-d H:i:s");

            switch($type){
                case 'comment':
                    $message = $userLoggedInName . " Commented on your post";
                    break;
                case 'like':
                    $message = $userLoggedInName ." Liked your post";
                    break;
                case 'profile_post':
                    $message = $userLoggedInName . " Posted on your profile";
                    break;
                case 'comment_non_owner':
                    $message = $userLoggedInName . " commented on a post you are follwing";
                    break;
                case 'profile_comment':
                    $message = $userLoggedInName . " commented on your profile post";
                    break;
            }
            $link = "post.php?id=".$post_id;
            $insert_query = mysqli_query($this -> con, "INSERT INTO notification VALUES(NULL,'$user_to','$userLoggedIn','$message','$link','$date_time','no','no')");
    }

}
?>