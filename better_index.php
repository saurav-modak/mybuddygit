<?php
    //TODO Title:       Better way to load post
    ////?  Used         This file is not part of the website.
    //!Required file :  includes/handlers/better_ajax_load_posts.php Post.php.BetterloadPostsFriends()
    //? This ajax file loads limited number of posts on evey myqli_query so it is faster and also 
    //? you dont get repeated reqults like now.
?><?php
include("includes/header.php");
//include("includes/classes/User.php");
//include("includes/classes/Post.php");
//session_destroy(); //for logout
//print_r($user);



// ! SSubmiting Post ----------------------------------
if(isset($_POST['post'])){
    $post = new Post($con,$userLoggedIn);
    $post->submitPost($_POST['post_text'],'none');
}
// !----------------------x-----------------------------
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
        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST">
                <textarea name="post_text" id="post_text" placeholder="Whats new?"></textarea>
                <input type="submit" name="post" id="post_button" value="Post">
            </form>
            <hr>
 
            <div class="posts_area" id="posts_area">

            </div>
            
                <div class="loading" style="text-align:center">
                    <img class="loadingImg" id="loadingImg" alt="Loading" src="assets\images\icons\ajax-loader.gif">
                </div>
                <div class="noMorePosts">
                    <p style="text-align:center">
                        No More Posts To Show
                    </p> 
                </div>
            
        </div>
        
        <script type="text/javascript">
            var start = 0;
            var limit = 10;
            var reachedMax = false;

            $('.loading').show();  //shows loading gif
            $('.noMorePosts').hide(); //hides no more posts text

            $(window).scroll(function () { //works when scrolled
                if (Math.round(window.scrollY + window.innerHeight) >= Math.round(document.body.scrollHeight))
                    getData();
                    console.log("start:"+start);
                    console.log("limit:"+limit);
            });

            $(document).ready(function () {//works when document is first
               getData();
            });

            function getData() {
                if (reachedMax)
                    return;

                $.ajax({
                   url: 'includes/handlers/better_ajax_load_posts.php',
                   method: 'POST',
                    dataType: 'text',
                   data: {
                       getData: 1,
                       start: start,
                       limit: limit
                   },
                   success: function(response) {
                        if (response == "reachedMax"){
                            reachedMax = true;
                            $('.loading').hide();  //hides loading gif
                            $('.noMorePosts').show(); //shows no more posts text
                        }
                        else {
                            start += limit;
                            $(".posts_area").append(response);  
                        }
                    }
                });
            }
        </script>
        </div>
    </body>
</html>