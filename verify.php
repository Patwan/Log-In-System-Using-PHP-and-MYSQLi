<?php 
/* Verifies registered user email, the link to this page
   is included in the register.php email message 
*/
require 'db.php';
session_start();

//Returns true if email is taken from the database and is not empty AND hash is set and is not empty
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    $email = $mysqli->escape_string($_GET['email']); 
    $hash = $mysqli->escape_string($_GET['hash']); 
    
    // Select user with matching email and hash, who hasn't verified their account yet (active = 0 meaning boolean false)
    $result = $mysqli->query("SELECT * FROM users WHERE email='$email' AND hash='$hash' AND active='0'");
	
	//Returns true if the number of rows is false(0 is a boolean meaning false)
    if ( $result->num_rows == 0 ){ 
        $_SESSION['message'] = "Account has already been activated or the URL is invalid!";
		header("location: error.php");
    }
    else {
        $_SESSION['message'] = "Your account has been activated!";
        
        // Set the user status to active (active = 1)
        $mysqli->query("UPDATE users SET active='1' WHERE email='$email'") or die($mysqli->error);
        $_SESSION['active'] = 1;
        header("location: success.php");
    }
}
else {
    $_SESSION['message'] = "Invalid parameters provided for account verification!";
    header("location: error.php");
}     
?>