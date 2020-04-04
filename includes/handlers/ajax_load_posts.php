<?php
    include("../../config/config.php");
    include("../classes/User.php");
    include("../classes/Post.php");

    $limit=20; //Number of posts to be loaded per call
    $posts= new Post($con, $_REQUEST['userLoggedIn']);

    $posts->loadPostsFriends($_REQUEST,$limit);

    /* Testing Purpose
    DEL> <    
    */ /* var_dump($_REQUEST,$limit); //see what is sending in Post function call
    
    $req = array( "page" => "1",
                "userLoggedIn" => "saurav_modak");

            $post = new Post($con,$userLoggedIn); //this is how Post function call works. take sone arry $con
            $post -> loadPostsFriends($req,15); //15 is the limit of posts we want
            var_dump($post); //

    /**/
            
?>