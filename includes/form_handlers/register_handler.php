<?php
//--------------------------Getting Submited Inputs-----------------------
//Declaring variable to prevent erroers
$fname="";
$lname="";
$em="";
$em2="";
$password="";
$password2="";
$date="";
$error_array=array();//to hold erroers while doing operations
$profile_pic="profile_pics\\\Defaults\\\profile-placeholder.jpg";

//echo $profile_pic;

if(isset($_POST['register_button'])){//chnecks if register_button was clicked
    //Stroing registration form values in variables

    //first name
    $fname=strip_tags($_POST['reg_fname']);//strip_tags converts any htm code input to normal text <a> to a
    $fname=str_replace(' ','',$fname);//replacing blank space from first name with nothing
    $fname=ucfirst(strtolower($fname));//Makes all letter lower case and converts first letter to upper case
    $_SESSION['reg_fname']=$fname; //storing in season (Line 2)

    //Last name
    $lname=strip_tags($_POST['reg_lname']);//strip_tags converts any htm code input to normal text <a> to a
    $lname=str_replace(' ','',$lname);//replacing blank space from first name with nothing
    $lname=ucfirst(strtolower($lname));//Makes all letter lower case and converts first letter to upper case
    $_SESSION['reg_lname']=$lname; //storing in season (Line 2)

    //Email
    $em=strip_tags($_POST['reg_em']);//strip_tags converts any htm code input to normal text <a> to a
    $em=str_replace(' ','',$em);//replacing blank space from first name with nothing
    $em=ucfirst(strtolower($em));//Makes all letter lower case and converts first letter to upper case
    $_SESSION['reg_em']=$em; //storing in season (Line 2)

    //Email 2
    $em2=strip_tags($_POST['reg_em2']);//strip_tags converts any htm code input to normal text <a> to a
    $em2=str_replace(' ','',$em2);//replacing blank space from first name with nothing
    $em2=ucfirst(strtolower($em2));//Makes all letter lower case and converts first letter to upper case
    $_SESSION['reg_em2']=$em2; //storing in season (Line 2)

    //Password
    $password=$_POST['reg_password'];//we dont mass with password
    $password2=$_POST['reg_password2'];
    
    //Date
    $date=date("Y-m-d");//current date

    if($em == $em2){
        //check if email is in correct format
        if(filter_var($em,FILTER_VALIDATE_EMAIL)){//Checking if email is in valid format, FILTER_VALIDATE_EMAIL is phps (version 5.2.0+) inbuilt email validation fiter 
            $em = filter_var($em,FILTER_VALIDATE_EMAIL);//Updating $em with validated email, FILTER_VALIDATE_EMAIL also validates errors in email address

            //Check if email already exists in user database
            $e_check = mysqli_query($con,"SELECT email FROM users WHERE email='$em'");//it should return nothing if email is not used before
            
            //count the number of rows returned
            $num_rows=mysqli_num_rows($e_check);
            if($num_rows>0){
                array_push($error_array,"Email already in use<br>");//saving in error_array
            }
        }else{
            array_push($error_array,"Invalid format<br>");//saving in error_array
        }

    }else{
        array_push($error_array,"Emails don't match<br>");//saving in error_array
    }

    if(strlen($fname)>25 || strlen($fname)<2){
        array_push($error_array,"Your first name must be between 2 and 25 characters<br>");//saving in error_array
    }
    if(strlen($lname)>25 || strlen($lname)<2){
        array_push($error_array,"Your lirst name must be between 2 and 25 characters<br>");//saving in error_array
    }

    if($password!=$password2){
        array_push($error_array,"Your password do not match<br>");//saving in error_array
    }else{
        if(preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]
        {6,15}$/',$password)){
            array_push($error_array, "Password must contain 6 characters of letters, numbers and 
            at least one special character.<br>");//saving in error_array
        }
    }
    if(strlen($password)>30 || strlen($password)<6){
        array_push($error_array,"Your password must be between 6 and 30 characters<br>");//saving in error_array
    }
    /*
        //se contents of the array for debugging purpose
        print_r($error_array);
    */
    //if not erroer is found
    //print_r($error_array);
    if(empty($error_array)){
        $password=md5($password);//Encrypt password before sending to database

        //Generate username by concatenating first name and last name
        $username = strtolower($fname."_".$lname);
        $check_username_query=mysqli_query($con,"SELECT username FROM users WHERE username='$username'");

        $i=0;
        //if username exists add number to username
      
        while(mysqli_num_rows($check_username_query) != 0){
            $i++;
            $username2=$username.$i;
            $check_username_query=mysqli_query($con,"SELECT username FROM users WHERE username='$username2'");
        }
        if($i==0){
            $username2=$username;
        }
        
        
        
        //echo "$fname $lname $em $password $username2 $i";

        $query=mysqli_query($con,"INSERT INTO users VALUES(NULL,'{$fname}','{$lname}','{$username2}','{$em}','{$password}','{$date}','{$profile_pic}','0','0','no',',','0','none')");
        
        array_push($error_array,"<span style='color:#14c800'>You're all set! Goahead and login!</span><br>");


        
        //empty searion variable
        $_SESSION=array();
        $_SESSION['log_email']=$em;//Store email into session variable to auto populate in login email
        
    }

}
?>