<?php
    //TODO Title:       Better way to load post
    ////?  Used         This file is not part of the website.
    //!Required file :  mybuddy2/better_index.php Post.php.BetterloadPostsFriends() User.php
    //?                 This ajax file loads limited number of posts on evey myqli_query so it is faster  
	//?                 and also don't give repeated reqults like now.
	//DONE				1) Somehow i need to convert this into a function of Post.php class


	// include("../../config/config.php"); // Getting reachedMax reachedMax reachedMax output if useing this instead 
	session_start(); //just using seasion_start() form config file.

    include("../classes/User.php");
    include("../classes/Post.php");

	if (isset($_POST['getData'])) {
		$con = new mysqli('localhost', 'root', '', 'mybuddy');

	//Object oriented style vs Procedural style
		$start = $con->real_escape_string($_POST['start']);//$start = mysqli_real_escape_string($conn, $_POST['start']);
		$limit = $con->real_escape_string($_POST['limit']);//$limit = mysqli_real_escape_string($conn, $_POST['limit']);

		$posts= new Post($con, $_SESSION['username']);

		$posts->BetterloadPostsFriends($start,$limit);	
	}

	
?>