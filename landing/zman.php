<?php
include_once("../php_includes/check_login_status.php");
for ($i=0; $i < 1000; $i++) { 
	$sql = "insert into user_likes (osid, account_name) values (1108, 'imfinnaadmin')";
	$query = mysqli_query($db_conx,$sql);
	echo $i."<br/>";
}
?>