<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
?>


<html>
<head>
<title>Welcome to my buddy</title>

    <style type="text/css">

        #passbar{
            display: none;
        }
        #back_bar {
            
            
            width: 73%;
            background-color: grey;
            margin: 0 auto;
        }
        #front_bar {
            height: 15px;
            width: 0%;
            height: 8px;
            background-color: red;
    }
    </style>

    <link rel="stylesheet" type="text/css" href="assets\css\register_style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    
</head>
<body>
    <?php
        //This keeps registration form open to show error if user writes something wrong.
        if(isset($_POST['register_button']) && !in_array("<span style='color:#14c800'>You're all set! Goahead and login!</span><br>", $error_array)){
            echo '
            <script>
            $(document).ready(function(){
                $("#first").hide();
                $("#second").show();

            });
            </script>
            ';}

        //opens the login form if user succesfully registared
        if (isset($_POST['register_button']) && in_array("<span style='color:#14c800'>You're all set! Goahead and login!</span><br>", $error_array)) {
            echo '
            <script>
            $(document).ready(function(){
                $("#first").show();
                $("#second").hide();

            });
            </script>
        ';}
	?>

    <div class="wrapper">
        <div class="login_box"  Onclick="hidepassbar();">
            <div class="login_header">
				<h1>MyBuddy</h1>
				Login or sign up below!
			</div>
            

            <!---------------------------Login Form------------------------------>
            <div id="first">
            <br>
                <form action="register.php" method="POST">
                
                    <?php //Shows registration successufull
                        if(in_array("<span style='color:#14c800'>You're all set! Goahead and login!</span><br>",$error_array)) {
                        echo "<span style='color:#14c800'>You're all set! Goahead and login!</span><br>"; 
                    }?>
                    <input type="email" name="log_email" placeholder="Email Address" value="<?php
                        if(isset($_SESSION['log_email'])) echo $_SESSION['log_email'];
                        ?>" required><br>
                    <input type="password" name="log_password" placeholder="Password"><br>
                    <input type="submit" name="login_button" value="Login"><br>

                    <?php if(in_array("Email or password was incorrect<br>",$error_array)){
                        echo "Email or password was incorrect<br>";
                        }?>
                    <br>
					<a href="#" id="signup" class="signup">Need an account? Register here!</a>
					<br>
                </form>
            </div>

            <!----------------------Registration form----------------------------->
            <div id="second">
                <br>
                <form action="register.php" method="POST">
                    <?php //            First name              ?>
                    <input type="text" name="reg_fname" placeholder="First Name" value="<?php
                    //showing previously entered value from session variable(line 2)
                    if(isset($_SESSION['reg_fname'])) echo $_SESSION['reg_fname']; ?>" required>
                    <br>
                    <?php //Displaying erroer if exists in erroer_array
                        if(in_array("Your first name must be between 2 and 25 characters<br>",$error_array)) echo "Your first name must be between 2 and 25 characters<br>" ?>

                    <?php //            Lirst name              ?>
                    <input type="text" name="reg_lname" placeholder="Last Name" value="<?php
                    //showing previously entered value from session variable(line 2)
                    if(isset($_SESSION['reg_lname'])) echo $_SESSION['reg_lname']; ?>" required>
                    <br>
                    <?php //Displaying erroer if exists in erroer_array
                        if(in_array("Your lirst name must be between 2 and 25 characters<br>",$error_array)) echo "Your lirst name must be between 2 and 25 characters<br>" ?>

                    <?php //              Email                ?>
                    <input type="text" name="reg_em" placeholder="Email" value="<?php
                    //showing previously entered value from session variable(line 2)
                    if(isset($_SESSION['reg_em'])) echo $_SESSION['reg_em']; ?>" required>
                    <br>


                    <?php //            Confirm Email              ?>
                    <input type="text" name="reg_em2" placeholder="Confirm Email" value="<?php
                    //showing previously entered value from session variable(line 2)
                    if(isset($_SESSION['reg_em2'])) echo $_SESSION['reg_em2']; ?>" required>
                    <br>
                    <?php
                    //Displaying erroer if exists in erroer_array
                        if(in_array("Email already in use<br>",$error_array)) echo "Email already in use<br>";
                        if(in_array("Invalid format<br>",$error_array)) echo "Invalid format<br>";
                        if(in_array("Emails don't match<br>",$error_array)) echo "Emails don't match<br>";
                    ?>


                    <input type="password" name="reg_password" placeholder="Password" Onkeyup="checkstrenght();" required><br>

                    <div id="passbar">
                        Password Strength:<br>
                        <div id="back_bar">
                            <div id="front_bar"></div>
                        </div><br>
                    </div>
                    <script>
                        
                        function hidepassbar(){
                            var passbar = document.getElementById("passbar");
                            passbar.style.display = "none";
                        }

                        function checkstrenght(){
                            var colorcheck = {
                                0: "#FF3301",
                                1: "#FF0101",
                                2: "orange",
                                3: "yellow",
                                4: "#43ff00"
                            }

                            var passbar = document.getElementById("passbar");
                            passbar.style.display = "block";

                            //Why getElementsByName('reg_password')[0].value;???
                            //document.getElementsByName returns a NodeList of elements. And a NodeList of elements does not have a .value property.
                            var password1=document.getElementsByName('reg_password')[0].value; //https://stackoverflow.com/questions/10306129/javascript-get-element-by-name
                            //console.log(password1);

                            var result = zxcvbn(password1);//https://github.com/dropbox/zxcvbn
                            /*zxcvbn is a password strength estimator inspired by password crackers. Through pattern matching and conservative estimation, it recognizes and weighs 30k common passwords, common names and surnames according to US census data, popular English words from Wikipedia and US television and movies, and other common patterns like dates, repeats (aaa), sequences (abcd), keyboard patterns (qwertyuiop), and l33t speak. */
                            var strength = result.score;
                            
                            var color = colorcheck[strength];
                            var percentage= strength*(100/4);
                            document.getElementById("front_bar").style.backgroundColor =color;
                            document.getElementById("front_bar").style.width = percentage+'%';
                            
                            console.log(color);
                            console.log(percentage);

                       }
                    </script>
    
                    <input type="password" name="reg_password2" placeholder="Confirm Password" required><br>
                    
                    <?php
                    //Displaying erroer if exists in erroer_array
                        if(in_array("Your password do not match<br>",$error_array)) echo "Your password do not match<br>";
                        if(in_array("Password shuld contain 6 characters of letters, numbers and 
                        at least one special character.<br>",$error_array)) echo "Password shuld contain 6 characters of letters, numbers and 
                        at least one special character.<br>";
                        if(in_array("Your password must be between 6 and 30 characters<br>",$error_array)) echo "Your password must be between 6 and 30 characters<br>";
                    ?>
                    
                    <input type="Submit" name="register_button" value="Register"><br>
                    
                    
					<a href="#" id="signin" class="signin">Already have an account? Login here!</a>
                    <br>
                    <br>
                    <?php
                        error_reporting(E_ALL); 
                        ini_set('display_errors',1);
                        ob_start();
                        flush(); // Flush the buffer
                        ob_flush();
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>