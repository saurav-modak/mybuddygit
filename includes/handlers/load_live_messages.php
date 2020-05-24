
<?php
//---------------for notification dropdown-------------------
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Message.php");

$message = new Message($con,$_REQUEST['userLoggedIn']);
echo $message->getLiveMessages($_REQUEST['user_to']);

?>
