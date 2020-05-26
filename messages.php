<?php
include("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);
if(isset($_GET['u']))
    $user_to = $_GET['u'];
else{
    $user_to = $message_obj->getMostRecentUser();
    if($user_to== false)
        $user_to= 'new';//posssible issue: if someone created a account with "new" username
}

if($user_to != "new")
    $user_to_obj = new User($con, $user_to);
if(isset($_POST['post_message'])){
    if(isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj -> sendMessage($user_to, $body, $date);
    }
}

?>
<div class="user_details_wrap">
    <div class="user_details column">
            <a href="<?php echo $userLoggedIn ?>"><img src="<?php echo $user['profile_pic']; ?>"></a>
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
    <div class="conver column" id="conversations">
                         
                        <a href="messages.php?u=new">Send a New Message</a><br>
                        <hr>
                        
                        <div class="load_conversations">
                            <?php echo $message_obj -> getConvos() ?>
                        </div>
                        <br>
                        
    </div>
</div>

<div class="main_column column" id="main_column">
        <?php

            if($user_to !="new"){
                echo "<h5>You and <a href='$user_to'>".$user_to_obj->getFirstAndLastName()."</a><hr></h5>";
                echo "<div class='load_messages'>";
                    echo $message_obj -> getMessages($user_to);
                echo "</div>";
            }else{
                echo "<h4>New Message</h4>";
            }

        ?>
        

        <div class="message_post">
            <form action="" method="POST">
                <?php
                    if($user_to == "new"){
                        echo "<p id='sectuser'> Select the friend you would like to message<br>To:</p>";
                        ?>
                        <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'></input>
                        <?php
                        echo "<div class='results'></div>";
                    }
                    else {
                        echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message...'></textarea>";
                        echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
                    }
                ?>
            </form>
        
        </div>
        
</div>

       <script>
            var userLoggedIn= '<?php echo $userLoggedIn; ?>';
            var user_to= '<?php echo $user_to; ?>';
            var audio = new Audio('msg.mp3');
            
            $('.load_messages').scrollTop($('.load_messages')[0].scrollHeight);

                window.setInterval(function(){
                        console.log("Checking for messages!");
                        $.ajax({
                            url:"includes/handlers/load_live_messages.php",
                            type: "POST",
                            data: "user_to="+user_to+"&userLoggedIn="+userLoggedIn,
                            cache: false,

                            success: function(data){
                                if($.trim(data)){
                                    audio.play();
                                    console.log("New Msg");
                                    $('.load_messages').append(data);
                                    $('.load_messages').scrollTop($('.load_messages')[0].scrollHeight);
                                    
                                }

                            }

                        })
                    
                }, 1000);//every 2seconds ajax sending new req
                
            
            /*
                //!           Tried makig the interval timer dynamic so that it slows down when user is not receving a msg for more than 5 seconds 
                    var userLoggedIn= '<?php echo $userLoggedIn; ?>';
                    var user_to= '<?php echo $user_to; ?>';
                    var audio = new Audio('msg.mp3');
                    var delay=1000;
                    var counting=1;
                    
                    $('.load_messages').scrollTop($('.load_messages')[0].scrollHeight);

                        window.setInterval(function(){
                                console.log("Checking for messages!<br>Delay:"+delay+"<br>Counting:"+counting);
                                $.ajax({
                                    url:"includes/handlers/load_live_messages.php",
                                    type: "POST",
                                    data: "user_to="+user_to+"&userLoggedIn="+userLoggedIn,
                                    cache: false,

                                    success: function(data){
                                        if($.trim(data)){
                                            audio.play();
                                            console.log("New Msg");
                                            $('.load_messages').append(data);
                                            $('.load_messages').scrollTop($('.load_messages')[0].scrollHeight);
                                            counting=1;
                                            delay=1000;

                                        }else{
                                            counting++;
                                                if(counting>5){
                                                delay=5000;
                                            }
                                        }
                                            
                                    }

                                })
                            
                        }, delay);
                        
                    
            */
            

            
            
        </script>

        