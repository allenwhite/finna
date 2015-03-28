<?php
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	include_once("php_includes/db_conx.php");
	$sesh = $_SESSION["username"];
	$sql = "SELECT id FROM users WHERE username='$sesh' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if ($uname_check > 0) {
	    header("location: message.php?msg=You are already logged in, homeboy");
    	exit();
    }
}
?><?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("php_includes/db_conx.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
//        ajax.send("u="+u+"&ph="+ph1+ph2+ph3+"&p="+p1); 
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	//$ph = preg_replace('#[^0-9]#i', '', $_POST['ph']);
	$ph = $_POST['ph'];
	$p = $_POST['p'];
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE phone='$ph' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$ph_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $ph == "" || $p == ""){
		echo "error...";
        exit();
	} else if ($u_check > 0){ 
		echo "error...";
        exit();
	} else if ($ph_check > 0){ 
		echo "error...";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "error...";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo "error...";
        exit();
    }else if(!is_numeric($ph)){
    	echo "error...";
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		//gotta make this shit better, hash and encrypt that shib
		$p_hash = md5($p);
		// Add user info into the database table for the main site table
		$u = strtolower($u);
		$sql = "INSERT INTO users (username, phone, password, ip, signup, lastlogin, noteschecked, activated)       
		        VALUES('$u','$ph','$p_hash','$ip',now(),now(),now(),'1')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username) VALUES ('$uid','$u')";
		$query = mysqli_query($db_conx, $sql);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
		}
		$sql = "INSERT INTO friends(user1, user2, datemade) VALUES('imfinnaadmin','$u',now())";
		$query = mysqli_query($db_conx, $sql); 
		echo "signup_success";
		exit();
	}
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Finna Sign Up?</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style/style.css">
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function restrict(elem){
	var tf = _(elem);
	var rx = new RegExp;
	if(elem == "phone1" || elem == "phone2" || elem == "phone3"){
		rx = /[^0-9]/g;
	} else if(elem == "username"){
		rx = /[^a-z0-9]/gi;
	}
	tf.value = tf.value.replace(rx, "");
}
function emptyElement(x){
	_(x).innerHTML = "";
}
function checkusername(){
	var u = _("username").value;
	if(u != ""){
		_("unamestatus").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("unamestatus").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("usernamecheck="+u);
	}
}
function signup(){
	var u = _("username").value;
	var ph1 = _("phone1").value;
	var ph2 = _("phone2").value;
	var ph3 = _("phone3").value;
	var p1 = _("pass1").value;
	var p2 = _("pass2").value;
	var status = _("status");
	if(u == "" || ph1 == "" || ph2 == "" || ph3 == ""|| p1 == "" || p2 == ""){
		status.innerHTML = "Fill out all of the form data";
	} else if(p1 != p2){
		status.innerHTML = "Your password fields do not match";
	} else {
		_("signupbtn").style.display = "none";
		status.innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "error..."){
					status.innerHTML = ajax.responseText;
					_("signupbtn").style.display = "block";
				} else {
					// window.scrollTo(0,0);
					// _("signupform").innerHTML = "Thanks "+u+"! Your account has been activated. <a href='login.php'>Click here to log in.</a>";
					login(u,p1);
				}
	        }
        }
        ajax.send("u="+u+"&ph="+ph1+ph2+ph3+"&p="+p1);
	}
}
function login(u,p){
	if(u == "" || p == ""){
		_("status").innerHTML = "Fill out all of the form data";
	} else {
		
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "login.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "login_failed"){
					_("status").innerHTML = "Login unsuccessful, please try again.";
				} else {
					window.location = "index.php";
				}
	        }
        }
        ajax.send("u="+u+"&p="+p);
	}
}
</script>
</head>
<body style="background:url(https://secure.static.tumblr.com/ecb2a659f3dacc1907eb9a0ed75eb5a1/kcebonh/sQRn8bmxs/tumblr_static_wrbkf.jpg),black;
			background-position: 700px;
			background-size: 400%; color:white;">
<div id="pageMiddle">
<div id="pageMiddle">
  <h3 style="margin-left:5%; margin-top:20px; margin-bottom:10px; font-size:200%; text-align:center; width:90%;">Sign Up Here</h3>
  <form name="signupform" id="signupform" onsubmit="return false;">
    <div><b>Username:</b></div>
    <input id="username" onkeyup="restrict('username')" onblur="checkusername()" type="text" maxlength="16">
    <span id="unamestatus"></span>
    <div><b>Phone Number:</b></div>
    <input id="phone1" onfocus="emptyElement('status')" onkeyup="restrict('phone1')" type="text" maxlength="3">&nbsp;-&nbsp;<input id="phone2" onfocus="emptyElement('status')" onkeyup="restrict('phone2')" type="text" maxlength="3">&nbsp;-&nbsp;<input id="phone3" onfocus="emptyElement('status')" onkeyup="restrict('phone3')" type="text" maxlength="4">
    <div><b>Create Password:</b></div>
    <input id="pass1" onfocus="emptyElement('status')" type="password" maxlength="16">
    <div><b>Confirm Password:</b></div>
    <input id="pass2" onfocus="emptyElement('status')" type="password" maxlength="16">
    <br /><br />
    <button id="signupbtn" class="statusbutts" onclick="signup()" style="margin:0px;" >Create Account</button>
    <br/>
    <span id="status"></span>
    <div><a style="color:white;" href="login.php">Already have an account?</a></div>
  </form>
</div>
</body>
</html>