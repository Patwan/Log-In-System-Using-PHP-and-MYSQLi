<?php 
ob_start(); //Important to remove headers
session_start();

require 'db.php';

$firstname= $lastname= $email= $password= '';
$emptyErr= $fNameErr = $lNameErr= $emailErr= $pswadErr= '';

/*To boost security effectively, always sanitize your form or login system using both clent-side and server-side validation
Hackers may use browsers that dont support client-side validation, hence server side validation will save you*/

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
			//Stores boolean true in variable valid
			$valid = true;
			
			//Returns true if firstname or lastname or email or passowrd fields are empty
			if(empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email']) || empty($_POST['password'])){
				$valid= false;
				$emptyErr= "Manadatory fields are missing. Please Fill them out";
			}
			
			else{
				$fname = test_input($_POST['firstname']);
				//remove illegal characters from name
				$fnm =filter_var($fname, FILTER_SANITIZE_STRING);
				if (!preg_match("/^[a-zA-Z\s,.`-]*$/", $fnm)) {
					$valid = false;
					$fNameErr= "Firstname: Please insert letters and spaces only";
				}			
			
				//Sanitize and validate last name
				$lname = test_input($_POST['lastname']);
				//remove illegal characters from name
				$lnm =filter_var($lname, FILTER_SANITIZE_STRING);
				if (!preg_match("/^[a-zA-Z\s,.`-]*$/", $lnm)) {
					$valid = false;
					$lNameErr= "Lastname: Please insert letters and spaces only";
				}
				
				//Sanitize and validate email address
				$email = test_input($_POST["email"]);
				//Remove all illegal characters from email
				$em = filter_var($email, FILTER_SANITIZE_EMAIL);
				//Validate email
        
				if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $em )){
					$valid = false;
					$emailErr = "Email: Please insert a valid email address";
				}
				
				//Sanitize and validate password
				$pw= $_POST['password'];
				//Checks UPPERCASE letters
				$uppercase = preg_match('@[A-Z]@', $pw);
				//Checks LOWERCASE LETTERS
				$lowercase = preg_match('@[a-z]@', $pw);
				//Checks number
				$number    = preg_match('@[0-9]@', $pw);
				
				//Returns true if the string length of the password is less than 8 and is not uppercase or not lowercase or not a number
				if(!$uppercase || !$lowercase || !$number || strlen($pw) < 8) {
					$valid = false;
					$pswadErr = "Password: Must contain atleast ONE UPPERCASE LETTER, ONE LOWERCASE LETTER, A NUMBER and must me a minimum of eight characters.";
				}
			
			
			//Returns true if there is a valid boolean tue
			if($valid){
				// Set session variables to be used on profile.php page
				$_SESSION['email'] = $em;
				$_SESSION['first_name'] = $fnm;
				$_SESSION['last_name'] = $lnm;

				// Escape all $_POST variables to protect against SQL injections
				$first_name = $mysqli->real_escape_string($fnm);
				$last_name = $mysqli->real_escape_string($lnm);
				$email = $mysqli->real_escape_string($em);
	
				//password_hash()- Creates a password hash using a strong one-way hasshing algorithm
				//PASSWORD_BCRYPT- Uses the CRYPT_BLOWFISH algorithm to create the hash. It will produce a 60 string hash
				$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
				//Creates a random number between 0 and 1000 and hashes the random number using md5 function then escapes the string
				$hash = $mysqli->escape_string( md5( rand(0,1000) ) );
      
				// Check if user with that email already exists
				//Select everthing from user table where email is equal to user input of email
				$result = $mysqli->query("SELECT * FROM user WHERE email='$email'") or die($mysqli->error());

				// We know user email exists if the rows returned are more than 0
				//mysqli_num_rows()- Returns the number of rows in a result set
				if ( $result->num_rows > 0 ) {
    
					$_SESSION['message'] = 'User with this email already exists!';
					header("location: error.php");
				}
	
				// Email doesn't already exist in a database, proceed...
				else { 
					// active is 0 by DEFAULT (no need to include it here)
					$sql = "INSERT INTO user (first_name, last_name, email, password, hash) " 
							. "VALUES ('$first_name','$last_name','$email','$password', '$hash')";

					//Returns true if the query against the database is successful
					if ( $mysqli->query($sql) ){
			
						//0 or false until user activates their account with verify.php
						$_SESSION['active'] = 0; 
			
						// So we know the user has logged in
						$_SESSION['logged_in'] = true; 
						$_SESSION['message'] ="Confirmation link has been sent to $email, please verify"
											. "your account by clicking on the link in the message!";

						// Send registration confirmation link (verify.php)
						$to      = $email;
						$subject = 'Account Verification (Patwan Website Solutions)';
						
						//When adding a space between two variables add a concatenation operator(.) before and after the variable meaning a space before and after the variable
						$message_body = 'Hello '.$first_name.', Thank you for signing up! Please click this link to activate your account:'
										. 'http://localhost/Practice/Registration2/verify.php?email='.$email.'&hash='.$hash;
						
						//mail headers (\r\n means a line break)
						$headers='From: info@pwebk.com\r\n' . 'Reply-To: info@pwebk.com';
						
						//mail function DOES NOT WORK ON LOCALHOST BUT ONLY ON A LIVE SERVER
						mail( $to, $subject, $message_body, $headers);
						
						//After form has been sent, redirect to profile.php page
						header("location: profile.php"); 
					}
		
					//if the query against the database is not successful
					else {
						$_SESSION['message'] = 'Registration failed!';
						header("location: error.php");
					}

				}
			}
		}
	  
	}
	
function test_input($data){
	trim($data);
	stripslashes($data);
	htmlspecialchars($data);
	return $data;
}
	
?>

<!DOCTYPE html>  
<html lang= "en-US"> 
<head> 
	<meta name= "robots" content= "noindex,follow">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!-- Favicon must be 16 by 16 or 32 by 32 pixels and must be .png or .gif-->
	<link rel="icon" type="image/x-icon" href="images/favicon.ico">  
	<meta name= "author" content= "Martin">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=3.0">
	<meta name="HandheldFriendly" content="true">

	<title> Sign Up form </title> 
	<link rel="stylesheet" type="text/css" href="style1.css">
	<!--Links to Google fonts-->
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:regular,regularitalic,bold,bolditalic">
</head>
<body>

<div class="upper">
	<img src= "images/logo.png" id="logo" alt= "Logo"> 
		<div class="call">
		<i> Contact Us... </i> <br>
		<a href="tel:+254724154002" id="call1" rel="nofollow"> <img src= "images/call1.png" alt="Call Button" class="call2"> </a> <br>
		<img src= "images/email4.png" alt="Email Address" class="call2">
		</div>
    <div id="banner"> <img src="images/banner.jpg" alt= "Patwan Website Solutions banner"> </div>
</div>

<div class="navbar1">
	<div class="menu1">
	<a href="#" rel="nofollow"> <button class="icon"> Menu</button></a>
		<ul>
			<li> <a href="index.html"> Home </a> </li>
			<li> <a href="website_design.html"> Website Design <span class="arrow">&#9660; </span> </a>
				<ul>
					<li> <a href="#"> Custom Web Design</a></li>
					<li> <a href="#"> Website Maintenance</a></li>
					<li> <a href="#"> Blogs </a></li>
					<li> <a href="#"> E-Commerce Websites </a></li>
					<li> <a href="#"> CMS Websites <span class="arrow">  &rang;</span></a>
						<ul>
							<li><a href="#"> Wordpress</a></li>
							<li><a href="#"> Drupal </a></li>
							<li><a href="#"> Joomla </a></li>
							<li><a href="#"> Magento</a></li>
						</ul> 
					</li>  
				
				</ul>
			
			
			
			</li>
			<li> <a href="graphic_design.html"> Graphics Design <span class="arrow">&#9660; </span> </a>
				<ul>
					<li> <a href="#"> Logo Designs</a></li>
					<li> <a href="#"> Branding </a></li>
					<li> <a href="#"> Print Designs </a>
						<ul>
							<li><a href="#"> Business Cards</a></li>
							<li><a href="#"> Posters </a></li>
							<li><a href="#"> Wedding Cards </a></li>
							<li><a href="#"> Banners </a></li>
							<li><a href="#"> Billboards</a></li>
							<li><a href="#"> T-Shirts Printing </a></li>
						</ul> 
					</li>  
				
				</ul>
			</li>
			<li> <a href="digital_marketing.html"> Digital Marketing <span class="arrow">&#9660; </span> </a>
				<ul>
					<li> <a href="#"> Content Marketing</a></li>
					<li> <a href="#"> Social-Media Marketing </a></li>
					<li> <a href="#"> Email Marketing</a></li>
					<li> <a href="#"> Pay Per Click</a></li>
					<li> <a href="#"> S.E.O </a>
						<ul>
							<li><a href=""> Link Building </a></li>
							<li><a href=""> Images Optimization </a></li>
						</ul> 
					</li>  
				
				</ul>
			</li>
			<li> <a href="contact.php"> Contact Us </a></li>
		</ul>
	
	</div>
</div>

<div class="form">

<?php
if ($emptyErr != "" || $fNameErr != "" || $lNameErr != "" || $emailErr != "" || $pswadErr != "") { 
    echo "<span class='info'>";
	echo $emptyErr . "<br>";
    echo $fNameErr . "<br>";
	echo $lNameErr . "<br>";
	echo $emailErr . "<br>";
	echo $pswadErr . "<br>";
    echo "</span>";
}
?>


<br>
      
	<ul class="tab-group">
		<li class="tab active"><a href="#signup">Sign Up</a></li>
        <li class="tab"><a href="login.php">Log In</a></li>
	</ul>
      
<div class="tab-content">
<h1>Sign Up for Free</h1>
			
          
				<form action="signup.php" method="post" autocomplete="off">
          
					<div class="top-row">
						<div class="field-wrap">
							<label>
								<span class="req"></span>
							</label>
							<input placeholder="Firstname*" type="text" pattern="[a-zA-Z-`\s]{1,13}"  maxlength="13" title="Should contain letters and should be less than 13 characters" value='<?php echo $firstname;?>' required autocomplete="off" name='firstname'>
						</div>
        
						<div class="field-wrap">
							<label>
								<span class="req"></span>
							</label>
							<input placeholder="LastName*" type="text" pattern="[a-zA-Z-`\s]{1,13}" maxlength="13" title="Should contain letters and should be less than 13 characters"value='<?php echo $lastname;?>' required autocomplete="off" name='lastname'>
						</div>
					</div>

					<div class="field-wrap">
						<label>
							<span class="req"></span>
						</label>
						<input placeholder="Email Address*" type="email" maxlength="26" value='<?php echo $email;?>' required autocomplete="off" name='email'>
					</div>
          
					<div class="field-wrap">
						<label>
							<span class="req"></span>
						</label>
						<input placeholder="Set A Password*" type="password" pattern="[a-zA-Z]{3,}[0-9]{2,}[~!`@#$%^&*)=_-+({:;?>|\/<.,]{1}"  title="Atleast 3 letters, atleast two numbers and one special character" required autocomplete="off" name='password' value='<?php echo $password;?>'>
					</div>
          
					<button type="submit" class="button button-block" name="register">Register</button>
          
				</form>


			
</div> <!-- /form -->
 </div> 

</body>
</html>