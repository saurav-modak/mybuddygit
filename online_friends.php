<?php
//    include("includes/header.php");

?>

<html>
    <head>
        <title>

        </title>
        <style type="text/css">
            .online_friends_frame{
                width: 300px;
                height: auto;
                min-height: 200px;
                /*background-color: #ecf0f1 !important;*/
                /*padding: 10px;*/
            }
            .header{
                height: 40px;
                background-color: #43443f !important;
                display: table;
                width: 100%;
                text-align: center;
                color:white;
            }
            .header span{
                display: table-cell;
                vertical-align: middle;
                font-size:19px;
            }
            .online_friends{
                min-height: 600px;
                /*background-color: #ecf0f1;*/
                margin-top: 10px;
            }
        </style>
    </head>
    <body>

    </body>
</html>
<div class="online_friends_frame column">
    <div class="header">
      <span>Active Users</span> 
    </div>
        <?php
        $logged_in_user_obj = new User($con, $userLoggedIn);
            echo ($logged_in_user_obj ->online_users());
        ?>
</div>