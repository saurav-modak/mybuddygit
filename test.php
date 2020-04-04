<?php
$userLoged="sam";
$sender="Rocky";

if(isset($_POST[$sender]) AND $_POST[$sender]=='Accept'){
    header("Location: test.php");
    echo "$userLoged you and  $sender are friend now";
}
if(isset($_POST[$sender]) AND $_POST[$sender]=='Ignore'){
    header("Location: test.php");
    echo "$userLoged you have ignored $sender's friend request";
    
}
?>

<form action="test.php" method="POST">
                    <input type="submit" name="<?php echo $sender ?>" id="accept_button" value="Accept">
                    <input type="submit" name="<?php echo $sender ?>" id="ignore_button" value="Ignore">
 </form>