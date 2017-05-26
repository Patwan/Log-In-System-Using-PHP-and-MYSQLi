<?php
/* Displays all successful messages */
session_start();
?>


<!DOCTYPE html>
<html>
<head>
  <title>Success</title>
  <link href="style1.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="form">
    <h1><?= 'Success'; ?></h1>
    <p>
		<?php 
			if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ):
				echo $_SESSION['message'];    
			else:
			header( "location: login.php" );
			endif;
		?>
    </p>
    <a href="login.php"><button class="button button-block"/>Home</button></a>
</div>
</body>
</html>
