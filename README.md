# Log-In-System-Using-PHP-and-MYSQLi

This repo shows how to build a Login System using PHP and MySQLi. Basically, a new user registers and the data is stored in MySQL database. Next the User logs In on a separate page.If h/she has forgotten password, clicks on a link and is redirected to forgot password page, where he enters the email and the system checks if the email is stored in the database, if true a reset password link is send to the registered email. The User then clicks on the link to reset password. The Log In system also uses HTML5 input validation and PHP validation on the back-end to prevent malicious code from breaking the system.
The code is ready to use, you can redeploy it on your website and customize it to meet your needs.

## Installation 

1. Edit the file `db.php` and update the configuration information (like your hostname, username and database password etc).

2. Upload the entire 'source' folder  to your web site. 
    
2. For novice PHP programmers, please read the comments in the script to get a good grasp of the cocept.


### Creating the MySQL Database

Create database "accounts" and create tables "user" on your localhost or live server:

```sql
CREATE TABLE `user` (
  `id` char(23) NOT NULL,
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(150) NOT NULL DEFAULT '',
  `hash` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) 

ENGINE=InnoDB DEFAULT CHARSET=utf8;

```
### Setup the `db.php` file
```
<?php

	$host = 'localhost';
	$user = 'root';
	$pass = '';
	$db = 'accounts';
	
    $mysqli = new mysqli($host,$user,$pass,$db) or die($mysqli->error);

	if(!$mysqli){
		//Error Message
		die("Connection failed: " . $mysqli->connect_error);
	}

```
	
## License
This program is free software published under the terms of the GNU [Lesser General Public License](http://www.gnu.org/copyleft/lesser.html).
You can freely use it on commercial or non-commercial websites. 
