<?php
    use PHPMailer\PHPMailer\PHPMailer;

    function sendcode($key,$address){

        require("phpmailer/PHPMailer.php");
        require("phpmailer/SMTP.php");
        require("phpmailer/Exception.php");

        $mail = new PHPMailer();
        //$mail->SMTPDebug = 1; 
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail -> isSMTP();
        $mail -> Host='smtp.gmail.com';
        $mail -> SMTPAuth = true;
        
        $mail -> Username ='hello.sourav1@gmail.com';
        include("mailuserpass.php");//This file contains sensitive password
        $mail -> Password =$pass;

        $mail -> Port = 587;
        $mail -> SMTPSecure = 'tls';

        $mail -> isHTML(true);
        $mail -> setFrom('hello.sourav1@gmail.com','Sourav');
        $mail -> addAddress($address);

        $mail -> Subject = 'Password reset request from HiBuddy';
        $mail -> Body = '<p style="text-align:center">
                Hello <b>'.$address.'</b>,<br> Here is your OTP for reseting your account password on
                HiBuddy:<br><b><font color="red">'.$key.'</font></b><br> Copy this OTP excatly to the verification page to reset your password.
        <br>Please do not share this one time password with anyone
        <br>
        <b>From: HiBuddy social network</b>
        </p>
        ';


        if($mail->send())
            $_SESSION['codesent']="yes";   
    }

    

?>