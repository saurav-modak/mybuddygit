<?php
    include("includes/header.php");
    include("includes/handlers/post_upload_handler.php");
    //session_destroy(); //for logout
    //print_r($user);

/* ! SSubmiting Post ----------------------------------

//*********** Moved to post_upload_handler.php ******************

    if($upload_ok){
        $post = new Post($con,$userLoggedIn);
        $post->submitPost($_POST['post_text'],'none');
    }
    
//!----------------------x-----------------------------*/
?>
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


    <div class="main_column column" tabindex="0">
        <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
            
            <textarea name="post_text" id="post_text" placeholder="Whats new?"></textarea>
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" name="post" id="post_button" value="Post">

        </form>
        <hr>
        <?php /*
            $req = array( "page" => "1",
                "userLoggedIn" => "saurav_modak");

            $post = new Post($con,$userLoggedIn);
            $post -> loadPostsFriends($req,15); */
        ?>

        <div class="posts_area" id="posts_area"></div>
        <img class="loading" id="loading" alt="Loading" src="assets/images/icons/ajax-loader.gif">
    </div>
    
    <script >
    
    var userLoggedIn= '<?php echo $userLoggedIn; ?>';
    var noMorePosts = $('.posts_area').find('.noMorePosts').val();
    /*//!just running once because this function has some problems
        // TODO: This uses jquery to find value from apended(loaded) html page
        // TODO: If this function called quickly again and again sometimes it load before page is fully appended
        // TODO: So it loads previewes value from last loaded page
        // TODO: This makes last 10 posts again so we are calling this function only once
        // TODO: Then we set the value op var noMorePosts from Post.php class
    */
    $(document).ready(function(){
        $('#loading').show();
    
        //Original ajax request for loading first posts
        $.ajax({
            url:"includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn="+userLoggedIn,
            cache: false,

            success: function(data){
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });
        
        $(window).scroll(function(){
            var height = $('.posts_area').height();//Height of the div containing posts
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            /*//! var noMorePosts = $('.posts_area').find('.noMorePosts').val();
                //TODO: instead of running it every time scroll event happens run it once at the time of loading script
                //TODO: and value for noMorePost is suplied from Post.php's last if else block
            */ 
            /*if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false'){*/
            if((Math.round(window.scrollY + window.innerHeight) >= Math.round(document.body.scrollHeight))&& noMorePosts=='false'){
                //this one from https://stackoverflow.com/questions/9439725/javascript-how-to-detect-if-browser-window-is-scrolled-to-bottom 

                $('#loading').show();
                
               /* alert("testing");*/
                /*console.log(posts);*/
            
                var ajaxReq = $.ajax({
                    url:"includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page="+page+"&userLoggedIn="+userLoggedIn,
                    cache: false,

                    success: function(response){
                        if(noMorePosts == "false"){ //Hopefully This check statement will prevent loading last 10 posts again
                            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage

                            $('#loading').hide();
                            $('.posts_area').append(response);
                        }
                    }
                });
            
            } //end if
            return false;
            
        }) //End $(window).scroll(function(){
    });


    </script>
    
</div>
</body>
</html>
