<?php
include("includes/header.php");

if(isset($_GET['q'])){
    $query = $_GET['q'];
}else{
    $query = "";
}

if(isset($_GET['type'])){
    $type = $_GET['type'];
}else{
    $type = "name";
}
?>
<div class="main_column column" id="main_column">
    <?php
        if($query==NULL){
            echo "You must enter something in searchbox.";
        }else{
            //echo "You have entered= '".$query."', and Type is= '".$type."'.";
            
            $names = explode(" ", $query);

            //If Query contains an underscore, assume user is searching for usernames
            if(strpos($query,'_')!== false)
                $usersReturnQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
            //If there are two words, assueme they are first and last names respectively
            else if(count($names)==2)
                $usersReturnQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%' ) AND user_closed='no' LIMIT 8");
            //If query has one word only, search first names or last names
            else if(count($names)==1)
                $usersReturnQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%' ) AND user_closed='no' LIMIT 8");

        } 

        //check if results were found
        if(mysqli_num_rows($usersReturnQuery) == 0)
            echo "We can't find anyone with a ".$type." like: ".$query;
        else
            echo mysqli_num_rows($usersReturnQuery)." results found: <br><br>";
        

        echo "<p id='grey'> Try searching for :</p>";
        echo "<a href='search.php?q=".$query."&type=name'>Names</a>, <a href='search.php?q=".$query."&type=username'>Usernames</a>";
      
       

        while($row = mysqli_fetch_array($usersReturnQuery)){
            $user_obj = new User($con,$user['username']);//$userLoggedIn
            $button ="";
            $mutual_friends ="";
            //echo $row['username'];
            if($user['username'] != $row['username']){
                //echo $row['username'];
                //Generate button depending on friendship status
                if($user_obj->isFriend($row['username']))
                    $button = "<input type='submit' name='".$row['username']."' class='danger'  value='Remove Friend'>";
                else if($user_obj->didReceiveRequest($row['username']))
                    $button = "<input type='submit' name='".$row['username']."' class='warning'  value='Respond to Request'>";
                else if($user_obj -> didSendRequest($row['username']))
                    $button = "<input type='submit' class='default'  value='Request Sent'>";
                else
                    $button = "<input type='submit' name='".$row['username']."' class='success'  value='Add Friend'>";
                $mutual_friends = $user_obj -> getMutualFriends($row['username'])." friends in common";

                //Button forms
            }
            echo "<div class='search_result'>
                    <div class='searchPageFriendButton'>
                        <form action='' method='POST'>
                            ".$button."
                            <br>
                        </form>
                    </div>
                    
                    <div class='result_profile_pic'>
                        <a href='".$row['username']."'><img src='".$row['profile_pic']."' style='height:100px;'></a>
                    </div>
                    <a href='".$row['username']."'>".$row['first_name']." ".$row['last_name']."
                    <p id='grey'>".$row['username']."</p>
                    </a>
                    ".$mutual_friends."
                    <hr>
                </div>
                ";
        }
    ?>
</div>
