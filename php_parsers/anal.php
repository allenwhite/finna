<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');
include_once("../php_includes/check_login_status.php");

$output = '';

//total users
$sql = "select count(username) from users";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
$something = $row["count(username)"];
$output .= '<b>total users: </b>'.$something."<br/>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//users registered for notifications
$sql = "select count(username) from users where apnsID!='null' OR gcm_regid!='null'";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
$something = $row["count(username)"];
$output .= '<b>users registered for notifications: </b>'.$something."<br/>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";


//total posts in the last week
$sql = "select count(data) from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY and type!='b'";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
$something = $row["count(data)"];
$output .= '<b>total posts (direct and public): </b>'.$something."<br/>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//total direct finnas in the last week
$sql = "select count(data) from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY and type='c'";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
$something = $row["count(data)"];
$output .= '<b>Total direct finnas: </b>'.$something."<br/>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//total public finnas in the last week
$sql = "select count(data) from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY and type='a'";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
$something = $row["count(data)"];
$output .= '<b>total public finnas: </b>'.$something."<br/>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//new users in the past week
$sql = "select username, phone, bio from users where signup >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY"; 
$query = mysqli_query($db_conx,$sql);
$output .= "<u><b>New users this week: </b></u></br>";
$output .= "<table>";
$output .= "<tr><td><b>u:</b></td><td><b>phone:</b></td><td><b>bio:</b></td></tr>";

while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$username = $row["username"];
	$phone = $row["phone"];
	$bio = $row["bio"];
	$output .= "<tr><td>$username</td><td>$phone</td><td>$bio</td></tr>";
}
$output .= "</table>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//new locations
$sql = "select location from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY and type!='b' and location!='null'";
$query = mysqli_query($db_conx,$sql);
$output .= "<b>locations from this week</b><br/>";
$output .= "<table>";

while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$something = $row["location"];
	$output .= "<tr><td>".$something."</td></tr>";	
}
$output .= "</table>";


$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//new times
$sql = "select time from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY and type!='b' and time!='null'";
$query = mysqli_query($db_conx,$sql);
$output .= "<b>times from this week</b><br/>";
$output .= "<table>";
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$something = $row["time"];
	$output .= "<tr><td>".$something."</td></tr>";	
}
$output .= "</table>";


$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//ten most finna'd statuses in the past week
$sql = "select osid from user_likes where osid in (
			select osid from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate()) + 6 DAY) 
		group by osid order by count(*) desc limit 10";
$query = mysqli_query($db_conx,$sql);
$output .= "<u><b>most finna'd statuses</b></u><br/>";
$output .= "<table>";
$output .= "<tr><td><b>user:</b></td><td><b>status:</b></td><td><b>time:</b></td><td><b>location:</b></td></tr>";

while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$osid = $row["osid"];
	$sql = "select author, data, time, location from status where osid='$osid' and type='a' ";
	$query2 = mysqli_query($db_conx,$sql);
	$row = mysqli_fetch_array($query2, MYSQLI_ASSOC);
	$author = $row["author"];
	$data = $row["data"];
	$time = $row["time"];
	$location = $row["location"];
	$output .= "<tr><td>$author</td><td>$data</td><td>$time</td><td>$location</td></tr>";	
}
$output .= "</table>";


$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//finna/invitation
$output .= "<u><b>finna/invitee ratio</b></u><br/>";
$sql = "select data, author, osid, type,postdate from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type!='b'";
$query = mysqli_query($db_conx,$sql);
$output .= "<table><tr><td><b>user</b></td><td><b>type</b></td><td><b>text</b></td><td><b>finna/invitee</b></td></tr>";
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$author = $row["author"];
	$type = $row["type"];
	$data = $row["data"];
	$output .= "<tr>";
	$output .= "<td>$author</td><td>$type</td><td>$data</td>";
	$osid = $row["osid"];
	if($type == 'a'){
		//friends for a type 'a' denominator
		$sql = "select count(user) from (select user1 as user from friends where user2='$author' and accepted='1'
			    union 
		    select user2 as user from friends where user1='$author' and accepted='1') as friendss";
		$query2 = mysqli_query($db_conx, $sql);
		$row = mysqli_fetch_array($query2, MYSQLI_ASSOC);
		$count = $row["count(user)"];
	}else{
		//direct friends for a type 'c' denominator
		$sql = "select count(username) from directMessages where osid='$osid'";
		$query2 = mysqli_query($db_conx, $sql);
		$row = mysqli_fetch_array($query2, MYSQLI_ASSOC);
		$count = $row["count(username)"];
	}

	//numerator for both cases, number of likes
	$sql = "select count(id) from user_likes where osid='$osid'";
	$query2 = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_array($query2, MYSQLI_ASSOC);
	$something = $row["count(id)"];

	$output .= "<td>$something/$count</td>";
	$output .= "</tr>";

}
$output .= "</table>";


$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//who wrote the most statuses?
$sql = "select count(data), author from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type!='b' group by author order by count(data) desc";
$query = mysqli_query($db_conx, $sql);
$output .= "<b>most posts (direct or public)</b><br/>";
$output .= "<table>";
$output .= "<tr><td><b># posts</b></td><td><b>user</b></td></tr>";
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$username = $row["author"];
	$numfinnas = $row["count(data)"];
	$output .= "<tr><td>$numfinnas</td><td>$username</td></tr>";
}
$output .= "</table>";



$output .= "<br/>";
$output .= "<hr/>";
$output .= "<br/>";



//who wrote the most posts and replies? (most engaged?)
$sql = "select count(data), author from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY group by author order by count(data) desc";
$query = mysqli_query($db_conx, $sql);
$output .= "<b>most engaged (most comments and posts and messages)</b><br/>";
$output .= "<table>";
$output .= "<tr><td><b># posts</b></td><td><b>user</b></td></tr>";
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$username = $row["author"];
	$numfinnas = $row["count(data)"];
	$output .= "<tr><td>$numfinnas</td><td>$username</td></tr>";
}
$output .= "</table>";




?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>Finna analytics</title>
</head>
<style type="text/css">
@font-face{
    font-family: avenir;
    src: url(AvenirLTStd-Book.otf);
}
@font-face{
    font-family: avenir;
    src: url(AvenirLTStd-Heavy.otf);
    font-weight: bold;
}
html{
    margin:0px;
    padding:0px;
}
body{
    margin:10px;
    font-family: avenir, "HelveticaNeue", "Helvetica Neue", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
    font-weight: 300;
    font-size: 13px;
    padding: 0px;
    background: rgba(27, 169, 187, 0.63);
}
td{
	padding:3px;
}
</style>
<body>
	<div id="pageMiddle">
		<?php
			echo $output;
		?>
	</div>
</body>
</html>