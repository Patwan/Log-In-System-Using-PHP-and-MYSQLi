<?php

session_start();
/* Registration process, inserts user info into the database 
   and sends account confirmation email message
 */

	// Set session variables to be used on profile.php page
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['first_name'] = $_POST['firstname'];
	$_SESSION['last_name'] = $_POST['lastname'];

	// Escape all $_POST variables to protect against SQL injections
	$first_name = $mysqli->real_escape_string($_POST['firstname']);
	$last_name = $mysqli->real_escape_string($_POST['lastname']);
	$email = $mysqli->real_escape_string($_POST['email']);
	
	//password_hash()- Creates a password hash using a strong one-way hasshing algorithm
	//PASSWORD_BCRYPT- Uses the CRYPT_BLOWFISH algorithm to create the hash. It will produce a 60 string hash
	$password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
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
			
			//mail() ONLY works on a live server on localhost use PHPMailer.
			mail( $to, $subject, $message_body );
			
			//After form has been sent, redirect to profile.php page
			header("location: profile.php"); 
		}
		
		//if the query against the database is not successful
		else {
			$_SESSION['message'] = 'Registration failed!';
			header("location: error.php");
		}

	}
	
?>