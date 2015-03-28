<?php
$sql = "SELECT bio FROM users WHERE username='$u' LIMIT 1";
$bio_query = mysqli_query($db_conx, $sql);
$biorow = mysqli_fetch_array($bio_query, MYSQLI_ASSOC);
$biostring = $biorow["bio"];


//total users
"select count(username) from users"

//new users in the past week
"select username, phone, bio from users where signup >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY"; 

//total posts in the last week
"select count(data) from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type!='b'";

//total direct finnas in the last week
"select count(data) from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type='c'";

//total public finnas in the last week
"select count(data) from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type='a'";

//post locations in the last week
"select location from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type!='b'";

//new locations
"select location from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type!='b' and location!='null'";

//new times
"select time from status where postdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY and type!='b' and time!='null'";

//ten most popular statuses (most finnas)
"select osid from user_likes group by osid order by count(*) desc limit 10";

?>