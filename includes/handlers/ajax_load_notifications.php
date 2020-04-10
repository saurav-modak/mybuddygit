<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Notification.php");

$limit = 7; //Number of messages to load

$not = new Notification($con, $_REQUEST['userLoggedIn']);
echo $not->getNotifications($_REQUEST, $limit);

?>