<?php 
session_start();
require 'db.php';

$emailErr= $emptyErr= $pswadErr= '';

//Initialize variables
$email= $password= '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		//Checks f login button is hit
		if (isset($_POST['login'])) {
			$valid= true;
			
			//Returns true if email or password fields are empty
			if(empty($_POST['email']) || empty($_POST['password'])){
				$valid= false;
				$emptyErr= "Manadatory Fields Are Missing. Please Fill Them Out.";
			}
			
			else{
				//Sanitize and Validate email
				$emm = test_input($_POST['email']);
				$em = filter_var($emm, FILTER_SANITIZE_EMAIL);
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
				$number= preg_match('@[0-9]@', $pw);
				
				//Returns true if the string length of the password is less than 8 and is not uppercase or not lowercase or not a number
				if(!$uppercase || !$lowercase || !$number || strlen($pw) < 8) {
					$valid = false;
					$pswadErr = "Password: Must contain atleast ONE UPPERCASE LETTER, ONE LOWERCASE LETTER, A NUMBER and must me a minimum of eight characters.";
				}
				
				if($valid){
				// Escape email to protect against SQL injections
				$email = $mysqli->escape_string($em);
	
				//Query that selects everthing(*) from table user where email is equal to $email
				$result = $mysqli->query("SELECT * FROM user WHERE email='$email'");
	
				//Returns true if number of rows is equal to 0(0 is a boolean meaning false)
				if ( $result->num_rows == 0 ){
					$_SESSION['message'] = "User with that email doesn't exist!";
					header("location: error.php");
				}
	
				//if the user exists
				else {
					//mysqli_fetch_assoc()- Fetches all the rows from the query from the database
					$user = $result->fetch_assoc();
		
					//Returns true if password verification of the user input and the one stored in the database
					if (password_verify($pw, $user['password']) ) {
			
						//Stores the email from the database in a session variable
						$_SESSION['email'] = $user['email'];
			
						//Stores the  first name from the database in a session variable
						$_SESSION['first_name'] = $user['first_name'];
			
						//Stores the lastname from the database in a session variable
						$_SESSION['last_name'] = $user['last_name'];
						$_SESSION['active'] = $user['active'];
        
						// This is how we'll know the user is logged in
						$_SESSION['logged_in'] = true;
			
						//Redirects to profile.php page where the session variables will be used
						header("location: profile.php");
					}
					else {
						$_SESSION['message'] = "You have entered wrong password, Please try again.";
						header("location: error.php");
					}
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
	
	<link rel="stylesheet" type="text/css" href="style1.css">
	<!--Links to Google fonts-->
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:regular,regularitalic,bold,bolditalic">
	<title> Log In Form</title>
</head>
<body>

<div class="upper">
	<img src= "images/logo.png" alt= "Logo"> 
		<div class="call">
		<i> Contact Us... </i> <br>
		<a href="" id="call1" rel="nofollow"> <img src= "images/call1.png" alt="Call Button" class="call2"> </a> <br>
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
//Returrns true if emptyErr is not equal to undefined or emailErr ios not equal to undefined
if ($emptyErr != "" || $emailErr != "" ||$pswadErr != "") { 
    echo "<span class='info'>";
	echo $emptyErr . "<br>";
	echo $emailErr . "<br>";
	echo $pswadErr . "<br>";
    echo "</span>";
}
?>
      
	<ul class="tab-group">
		<li class="tab"> <a href="signup.php">Sign Up</a></li>
        <li class="tab active"> <a href="#login">Log In</a></li>
	</ul>
      
		<div class="tab-content">

			   
				<h1>Welcome Back!</h1>
          
				<form action="login.php" method="post" autocomplete="off">
          
					<div class="field-wrap">
						<label>
							<span class="req"></span>
						</label>
						<input placeholder="Email Address*" maxlength="26" type="email" value="<?php echo $email;?>" required autocomplete="off" name="email"/>
					</div>
          
					<div class="field-wrap">
					<label>
						<span class="req"></span>
					</label>
					<input placeholder="Password*" type="password" pattern="[a-z]{1,}A-Z]{1,}[0-9]{1,}[~!`@#$%^&*)=_-+({:;?>|\/<.,]"  title="Atleast ONE UPPERCASE LETTER, ONE LOWERCASE LETTER, A NUMBER and must me a minimum of eight characters." required autocomplete="off" name="password"/>
					</div>
          
					<p class="forgot"><a href="forgot.php">Forgot Password?</a></p>
          
					<button class="button button-block" name="login">Log In</button>
          
				</form>
		
        
		</div><!-- tab-content -->
      
</div> <!-- /form -->

</body>
</html>
