<?php
/**
 *! This class takes connection variable and a username and returns therir info.
 Every functon returns diffrent info.
 */
    class User{
        private $user;
        private $con;

        public function __construct($con, $user){
            $this -> con = $con;
            $user_details_query=mysqli_query($con,"SELECT * FROM users WHERE username='$user'");
            $this-> user=mysqli_fetch_array($user_details_query); 
        }
/**
* ! This is dumb >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
*? Because we allready have complete user row in $row from constructor calling

            public function getFirstAndLastName(){
                $username = $this->user['username'];
                $query = mysqli_query($this->con,"SELECT first_name, last_name FROM users WHERE username='$username'");
                $row=mysqli_fetch_array($query);
                return $row['first_name']." ".$row["last_name"];
            }

* TODO: use this insstead>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

        public function getUsername(){
            return  $this->user['username'];
        }

        public function getUsernameByPostID($post_id){
            $query = mysqli_query($this->con,"SELECT added_by FROM `posts` WHERE id='$post_id'");
            $user=mysqli_fetch_array($query);
            return $user[0];
        } 


        public function getFirstAndLastName(){
            return  $this->user['first_name']." ". $this->user['last_name'];
        }

        public function getProfilePic(){
            return  $this->user['profile_pic'];
        }

        public function getFriendArray(){
            return  $this->user['friend_array'];
        }

        public function getNumPosts(){
            return  $this->user['num_posts'];
        }

        public function isClosed(){
            if($this->user['user_closed'] == 'yes'){
                return true;
            }else{
                return false;
            }
        }

        public function isFriend($username_to_check){
            $usernameComma = ",".$username_to_check.","; //adding comma to the username to match against firned array of user table
            /*
            echo "strstr: ".(strstr($this->user['friend_array'],$usernameComma));
            echo "user['friend_array']:";
            $abc = ($this->user['friend_array']);
            var_dump($abc);
            */
            //if user is friend or self
            if(strstr($this->user['friend_array'],$usernameComma) || $username_to_check == $this -> user['username'] )
                return true;
            else
                return false;//temorary all user posts are visible to each other make is false to turn it off
        }

        public function didReceiveRequest($user_from) {
            $user_to = $this->user['username'];
            $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
            if(mysqli_num_rows($check_request_query) > 0) {
                return true;
            }
            else {
                return false;
            }
        }

        public function didSendRequest($user_to) {
            $user_from = $this->user['username'];
            $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
            if(mysqli_num_rows($check_request_query) > 0) {
                return true;
            }
            else {
                return false;
            }
        }



        public function removeFriend($user_to_remove) {
            $logged_in_user = $this->user['username'];
    
            $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
            $row = mysqli_fetch_array($query);
            $friend_array_username = $row['friend_array'];
    
            $new_friend_array = str_replace($user_to_remove. ",", "", $this->user['friend_array']);
            $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");
    
            $new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
            $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");
        }

        public function sendRequest($user_to){
            $user_from = $this -> user['username'];
            $query = mysqli_query($this-> con,"INSERT INTO friend_requests VALUES(NULL,'$user_to','$user_from')");
        }

        public function getMutualFriends($user_to_check){
            $mutualFriends=0;
            $user_array = $this -> user['friend_array'];
            $user_array_explode = explode(",",$user_array);

            $query=mysqli_query($this->con,"SELECT friend_array FROM users WHERE username='$user_to_check'");
            $row=mysqli_fetch_array($query);
            $user_to_check_array = $row['friend_array'];
            $user_to_check_array_explode = explode(",", $user_to_check_array);

            foreach($user_array_explode as $i){

                foreach($user_to_check_array_explode as $j){

                    if($i ==$j && $i !=NULL){
                        $mutualFriends++;
                    }
                }
            }
            return $mutualFriends;
        }

        public function numFriendReq(){
            $me = $this->user['username'];
            $query=mysqli_query($this -> con,"SELECT * FROM friend_requests WHERE user_to='$me'");
            return mysqli_num_rows($query);
        }

        
        public function online_users(){
            $time_obj = new Time();
            $css="";
            $count=0;
            $active_friends ="";
            $active_users_query=mysqli_query($this->con,"SELECT * FROM login_status WHERE logged_out='no' ORDER BY last_login DESC");
        
            while($row=mysqli_fetch_array($active_users_query)){

                $log_time = $time_obj->timeframe($row['last_login']);
                $act_time = $time_obj->timeframe($row['last_activity']);
                //return $row[1];
                $user_info=mysqli_fetch_array(mysqli_query($this->con,"SELECT * FROM users WHERE id='$row[1]'"));
                
                if($this->user['username'] == $user_info['username'])
                    continue; //Skipping logged in user
                
                
                $count++;
                if($count>6) //if more than 7 results found it adds following id
                    $css="id='hover_scroll_y'"; //this id activates hover scroll css
                    
                    
                $active_friends .= "
                    <div class='individual'>
                        <a href='".$user_info['username']."'>
                        <img src='".$user_info['profile_pic']."'>
                        <div class='ind_texts'>
                            <div class='ind_name'>
                                ".$user_info['first_name']." ".$user_info['last_name']."
                                </div>
                            <div class='ind_log_time'>
                                Login: ".$log_time."
                            </div>
                            <div class='ind_act_time'>
                                Active: ".$act_time."
                            </div>
                        </div>
                        </a>
                        <a href='messages.php?u=".$user_info['username']."'>
                            <div class='ind_msg'>
                                <i class='fas fa-envelope'></i>
                            </div>
                        </a>
                    </div>";
            }
            if($count>0){
                return  "<div class='online_friends'".$css."'>".$active_friends."</div>";
            }else{
                return "<div class='online_friends no_friend'>No One Is Online </div>";
            }
            
        
        }
    }
    
?>