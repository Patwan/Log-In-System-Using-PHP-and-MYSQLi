<?php
	/* Log out process, unsets and destroys session variables */
	session_start();
	session_unset();
	session_destroy(); 
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Error</title>
  <link href="style1.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="form">
          <h1>Thanks for stopping by</h1>
              
          <p><?= 'You have been logged out!'; ?></p>
          
          <a href="login.php"><button class="button button-block"/>Home</button></a>

    </div>
</body>
</html>
