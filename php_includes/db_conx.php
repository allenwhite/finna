 <?php
//connects to mysql database.
//host you are connecting to, username, password, database name
$db_conx = mysqli_connect("localhost",  "alpal", "sexmoongulley", "finnaSocial");
//evaluate the connection
if(mysqli_connect_errno()){
	//change this later yo. echo a message or something
	echo mysqli_connect_error();
	exit();
}
if (!mysqli_set_charset($db_conx, "utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", mysqli_error($link));
}
?>