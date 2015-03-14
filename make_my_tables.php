<?php
include_once("php_includes/db_conx.php");

$tbl_users =	"CREATE TABLE IF NOT EXISTS users (
					id INT(11) NOT NULL AUTO_INCREMENT,
					username VARCHAR(24) NOT NULL,
					phone VARCHAR(10) NOT NULL,
					password VARCHAR(255) NOT NULL,
					avatar VARCHAR(255) NULL,
					ip VARCHAR(255) NOT NULL,
					signup DATETIME NOT NULL,
					lastlogin DATETIME NOT NULL,
					noteschecked DATETIME NOT NULL,
					activated ENUM('0','1') NOT NULL DEFAULT '0',
					PRIMARY KEY (id),
					UNIQUE(username),
					UNIQUE(phone)
				)";
//userlevel - promoted accounts
//notesChecked - notifications
//avatar - stores image file name
//activated - did they activate via email
$query = mysqli_query($db_conx, $tbl_users);
if($query === TRUE){
	echo "<h3>User table created successfulli :)</h3>";
}else{
	echo "<h3>User table not created :(</h3>";
}

/////////////////////////

$tbl_useroptions =	"CREATE TABLE IF NOT EXISTS useroptions (
					id INT(11) NOT NULL,
					username VARCHAR(16) NOT NULL,
					PRIMARY KEY (id),
					UNIQUE(username),
					FOREIGN KEY(id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
				)";
$query = mysqli_query($db_conx, $tbl_useroptions);
if($query === TRUE){
	echo "<h3>Useroptions table created successfulli :)</h3>";
}else{
	echo "<h3>Useroptions table not created :(</h3>";
}

/////////////////////////

$tbl_friends =	"CREATE TABLE IF NOT EXISTS friends (
					id INT(11) NOT NULL AUTO_INCREMENT,
					user1 VARCHAR(24) NOT NULL,
					user2 VARCHAR(24) NOT NULL,
					datemade DATETIME NOT NULL,
					accepted ENUM('0','1') NOT NULL DEFAULT '0',
					PRIMARY KEY (id),
					FOREIGN KEY(user1) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE,
					FOREIGN KEY(user2) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
				)";
$query = mysqli_query($db_conx, $tbl_friends);
if($query === TRUE){
	echo "<h3>friends table created successfulli :)</h3>";
}else{
	echo "<h3>friends table not created :(</h3>";
}



/////////////////////////

$tbl_status =	"CREATE TABLE IF NOT EXISTS status (
					id INT(11) NOT NULL AUTO_INCREMENT,
					osid INT(11) NOT NULL,
					account_name VARCHAR(16) NOT NULL,
					author VARCHAR(16) NOT NULL,
					type ENUM('a','b','c') NOT NULL,
					data VARCHAR(255) NOT NULL,
					postdate DATETIME NOT NULL,
					PRIMARY KEY (id),
					
				)";
///FOREIGN KEY(osid) REFERENCES status(id) ON DELETE CASCADE ON UPDATE CASCADE
$query = mysqli_query($db_conx, $tbl_status);
if($query === TRUE){
	echo "<h3>status table created successfulli :)</h3>";
}else{
	echo "<h3>status table not created :(</h3>";
}


/////////////////////////

// $tbl_photos =	"CREATE TABLE IF NOT EXISTS photos (
// 					id INT(11) NOT NULL AUTO_INCREMENT,
// 					user VARCHAR(16) NOT NULL,
// 					gallery VARCHAR(16) NOT NULL,
// 					filename VARCHAR(255) NOT NULL,
// 					description VARCHAR(255),
// 					uploaddate DATETIME NOT NULL,
// 					PRIMARY KEY (id)

// 				)";

// $query = mysqli_query($db_conx, $tbl_photos);
// if($query === TRUE){
// 	echo "<h3>photos table created successfulli :)</h3>";
// }else{
// 	echo "<h3>photos table not created :(</h3>";
// }


/////////////////////////

$tbl_notifications =	"CREATE TABLE IF NOT EXISTS notifications (
					id INT(11) NOT NULL AUTO_INCREMENT,
					username VARCHAR(24) NOT NULL,
					initiator VARCHAR(24) NOT NULL,
					app VARCHAR(255) NOT NULL,
					note VARCHAR(255) NOT NULL,
					did_read ENUM('0','1') NOT NULL DEFAULT '0',
					date_time DATETIME NOT NULL,
					PRIMARY KEY (id),
					FOREIGN KEY(note) REFERENCES status(data) ON DELETE CASCADE ON UPDATE CASCADE,
					FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE,
					FOREIGN KEY(initiator) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
				)";

$query = mysqli_query($db_conx, $tbl_photos);
if($query === TRUE){
	echo "<h3>notifications table created successfulli :)</h3>";
}else{
	echo "<h3>notifications table not created :(</h3>";
}

?>