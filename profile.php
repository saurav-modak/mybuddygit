<?php 
include("includes/header.php");



if(isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);

	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}



if(isset($_POST['remove_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->removeFriend($username);
}

if(isset($_POST['add_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->sendRequest($username);
}
if(isset($_POST['respond_request'])) {
	header("Location: requests.php");
}

 ?>

 	<style type="text/css">
	 	.wrapper {
	 		margin-left: 0px;
			padding-left: 0px;
	 	}

 	</style>
	
 	<div class="profile_left">
 		<img src="<?php echo $user_array['profile_pic']; ?>">

 		<div class="profile_info">
 			<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
 			<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
 			<p><?php echo "Friends: " . $num_friends ?></p>
 		</div>


         <!-- Add friend fucnction -->
 		<form action="<?php echo $username; ?>" method="POST">
 			<?php 
 			$profile_user_obj = new User($con, $username); 
 			if($profile_user_obj->isClosed()) {
 				header("Location: user_closed.php");
 			}

 			$logged_in_user_obj = new User($con, $userLoggedIn); 

 			if($userLoggedIn != $username) {

 				if($logged_in_user_obj->isFriend($username)) {
 					echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
 				}
 				else if ($logged_in_user_obj->didReceiveRequest($username)) {
 					echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
 				}
 				else if ($logged_in_user_obj->didSendRequest($username)) {
 					echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
 				}
 				else 
 					echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';

 			}
 			?>
			 <!-- Button trigger modal -->
			 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#post_form">Post Something</button>
			 
		 </form>
		 <?php
			if($userLoggedIn != $username){
				echo '<div class="profile_info_buttom">';
					echo $logged_in_user_obj -> getMutualFriends($username). " Mutual Friends";
				echo '</div>';
			}
		 ?>

 	</div>
	
	 <div class="profile_main_column column left">
		<nav>
			<div class="nav nav-tabs navbar navbar-dark bg-secondary rounded-pill" id="nav-tab" role="tablist">
				<a class="nav-item nav-link active rounded-pill" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">News Feed</a>
				<a class="nav-item nav-link rounded-pill" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">About</a>
				<a class="nav-item nav-link rounded-pill" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Messages</a>
			</div>
		</nav>
		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
				
				<div class="posts_area" id="posts_area"></div>
				<img class="loading" id="loading" alt="Loading" src="assets/images/icons/ajax-loader.gif">
				
			</div>
			<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
			<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>
		</div> 
	</div>
</div>

 

<!-- Modal
uses hibuddy.js to submit the post
-->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Post Something</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<p>This will appear on the user's profile and also their newsfeed for your friends to see!</p>

				<form class="profile_post" action="profile.php" method="POST">
			 		<div class="form-group">
			 			<textarea class="form-control rounded-5" name="post_body"></textarea>
						<input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
						<input type="hidden" name="user_to" value="<?php echo $username;//of the profile owner ?>">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
			</div>
			</div>
		</div>
		</div>
    </div>
	
	<script >
		/* Ajax call and infinite crolling */
		var userLoggedIn= '<?php echo $userLoggedIn; ?>';
		var profileUsername='<?php echo $username; ?>'

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
				url:"includes/handlers/ajax_load_profile_posts.php",
				type: "POST",
				data: "page=1&userLoggedIn="+userLoggedIn+"&profileUsername="+profileUsername,
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
						url:"includes/handlers/ajax_load_profile_posts.php",
						type: "POST",
						data: "page="+page+"&userLoggedIn="+userLoggedIn+"&profileUsername="+profileUsername,
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