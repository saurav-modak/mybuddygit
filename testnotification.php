<?php
include("includes\header.php");

$notification = new Notification($con,$userLoggedIn);
$notification->insertNotification("321","sam","profile_post");


?>