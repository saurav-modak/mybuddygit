<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Notification.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");




if(isset($_SESSION['username'])){
    $userLoggedIn=$_SESSION['username'];
    $user_details_query=mysqli_query($con,"SELECT *  FROM users WHERE username='$userLoggedIn'");
    $user=mysqli_fetch_array($user_details_query);
}else{
    header("location: register.php");
}
?>

<html>
    <head>
        <title>Welcome to MyBuddy</title>

        <!-- Javascript -->
        <!--
        Hotfix jaquery with older version bacuse of internet connection lost.
        Please change this to letest version as soon as possible. if you are reading this comment in future.
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        -->
        <script src="assets/js/jquery-2.2.3.min.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/bootbox.all.min.js"></script>
        <script src="assets/js/hibuddy.js"></script>
        <script src="assets/js/jcrop_bits.js"></script>
        <script src="assets/js/jquery.Jcrop.js"></script>

        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/fontawesome/css/all.css">
        <link rel="stylesheet" href="assets/css/jquery.Jcrop.css">
        
    </head>
    </head>
<body>   
    <div class="top_bar">

        <div class="logo">
            <a href="index.php">HyBuddy</a>
        </div>

        <div class="search" id="search">
            <form action="search.php" method="GET" name="search_form">
                <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>');" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
                <div class="button_holder">
                    <img src="assets/images/icons/magnifying_glass.png">
                </div>
            </form>
            <div class="search_results"></div>
            <div class="search_results_footer_empty"></div>
        </div>

        <nav>
            <?php
                //Unread messages
                $messages = new Message($con, $userLoggedIn);
                $num_messages = $messages -> getUnreadNumber();

                //Unread notifications
                $notifications = new Notification($con, $userLoggedIn);
                $num_notifications = $notifications -> getUnreadNumber();

            ?>
			<a href="<?php echo $userLoggedIn; ?>">
				<?php echo $user['first_name']; ?>
			</a>
			<a href="index.php">
				<i class="fas fa-home"></i>
			</a>
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message');">
				<i class="fas fa-envelope"></i>
                <span class="notification_badge" id="unread_message"><?php echo $num_messages; ?></span>
			</a>

            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <i class="fa fa-bell fa-lg"></i>
                <?php
                    if($num_notifications>0)
                        echo '<sapn class="notification_badge" id="unread_notification">'.$num_notifications.'</sapn>';
                ?>
            </a>

            <a href="requests.php">
				<i class="fas fa-users"></i>
			</a>

			<a href="settings.php">
				<i class="fas fa-cog"></i>
			</a>
			<a href="includes/handlers/logout.php">
				<i class="fas fa-sign-out-alt"></i>
				<!- session_destroy();//logout ->
			</a>

		</nav>
        <div class="dropdown_data_window" style="height:0px;"></div>
        <input type="hidden" id="dropdown_data_type" value=""/>
    </div>
    <div class="wrapper">