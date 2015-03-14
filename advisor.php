<?php
if(isset($_POST["company"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$e = mysqli_real_escape_string($db_conx, $_POST['email']);
	$n = $_POST['name'];
	if(isset($_POST['phone'])){
		$ph = $_POST['phone'];	
	}else{
		$ph = NULL;
	}
	$c = $_POST['company'];
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM advisor WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	$sql = "SELECT id FROM advisor WHERE company='$c' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$c_check = mysqli_num_rows($query);
	$sql = "SELECT id FROM advisor WHERE phone='$ph' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$ph_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($e == "" || $n == "" || $c == ""){
		echo "The form submission is missing values. <a href='http://www.npgsolutions.com/index.php/npg-advisor'><< Back</a>";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system <a href='http://www.npgsolutions.com/index.php/npg-advisor'><< Back</a>";
        exit();
	} else if ($c_check > 0){ 
        echo "That Company is already in use in the system <a href='http://www.npgsolutions.com/index.php/npg-advisor'><< Back</a>";
        exit();
	} else if ($ph_check > 0){ 
        echo "That phone number is already in use in the system <a href='http://www.npgsolutions.com/index.php/npg-advisor'><< Back</a>";
        exit();
	} else if(!is_numeric($ph) && $ph != ""){
    	echo "Please enter a valid phone number <a href='http://www.npgsolutions.com/index.php/npg-advisor'><< Back</a>'";
    } else {
    	$sql = "INSERT INTO advisor (email, contactname, company, phone, ip, signup) VALUES('$e','$n','$c','$ph','$ip',now())";
		$query = mysqli_query($db_conx, $sql);
		if($query === TRUE){
			header("location: http://www.npgsolutions.com/index.php/npg-advisor-congratulations");;
		}
    }
?>