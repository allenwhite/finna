<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
//crop function
function ak_img_resize($target, $newcopy, $w, $h, $ext) {
    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio) {
           $w = $h * $scale_ratio;
    } else {
           $h = $w / $scale_ratio;
    }
    $img = "";
    $ext = strtolower($ext);
    if ($ext == "gif"){ 
      $img = imagecreatefromgif($target);
    } else if($ext =="png"){ 
      $img = imagecreatefrompng($target);
    } else { 
      $img = imagecreatefromjpeg($target);
    }
    $tci = imagecreatetruecolor($w, $h);
    // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    
    imagejpeg($tci, $newcopy, 80);
    
}
?><?php 

if (isset($_FILES["img"]["name"]) && $_FILES["img"]["tmp_name"] != ""){
	$fileName = $_FILES["img"]["name"];
    $fileTmpLoc = $_FILES["img"]["tmp_name"];
	$fileType = $_FILES["img"]["type"];
	$fileSize = $_FILES["img"]["size"];
	$fileErrorMsg = $_FILES["img"]["error"];
	
	$extension = exif_imagetype($fileTmpLoc);
	//echo '~~'.$extension."~~\n";

	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	if($fileName != $fileExt){
		$db_file_name = rand(100000000000,999999999999).".".$fileExt;
	}else if($extension == 2){
		$db_file_name = rand(100000000000,999999999999).".jpg";
	}else if($extension == 3){
		$db_file_name = rand(100000000000,999999999999).".png";
	}

	
	if($fileSize > 1048576) {
		// header("location: ../message.php?msg=ERROR: Your image file was larger than 1mb");
		// exit();	
	} else if (!preg_match("/\.(gif|jpg|jpeg|png)$/i", $fileName) && $extension != 2 && $extension != 3 && $extension != 1) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$sql = "SELECT avatar FROM users WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	$avatar = $row[0];
	if($avatar != ""){
		$picurl = "../user/$log_username/$avatar"; 
	    if (file_exists($picurl)) { unlink($picurl); }
	}
	$moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name");
	chmod("../user/$log_username/$db_file_name", 0755);
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
	// include_once("../php_includes/image_resize.php");
	$target_file = "../user/$log_username/$db_file_name";
	$resized_file = "../user/$log_username/$db_file_name";
	//img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	//ResizeToDimension($hmax, $target_file, $extension, $resized_file);


//echo $target_file;
// $x_size = getimagesize($target_file)[0];
// $y_size = getimagesize($target_file)[1];
	$x_size = ImageSX($target_file);
	$y_size = ImageSY($target_file);

//the minimum of xlength and ylength to crop.
$crop_measure = min($x_size, $y_size);

// Set the content type header - in this case image/jpeg
//header('Content-Type: image/jpeg');

////$to_crop_array = array('x' =>round($x_size/2), 'y' => round($y_size/2), 'width' => $crop_measure, 'height'=> $crop_measure);
////$thumb_im = imagecrop($target_file, $to_crop_array);

$newImg = ak_img_thumb($target_file, $resized_file, $crop_measure, $crop_measure, $extension);

////imagejpeg($thumb_im, $resized_file, 100);
	$moveResult = move_uploaded_file($newImg, "../user/$log_username/$db_file_name");
	chmod("../user/$log_username/$db_file_name", 0755);


	$sql = "UPDATE users SET avatar='$db_file_name' WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	
}



if (isset($_POST['phone']) && $_POST['phone'] != ''){

	$phone = $_POST['phone'];
	if(strlen($phone) == 10){
		$sql = "UPDATE users SET phone='$phone' WHERE username='$log_username' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);	
	}else{
		header("location: ../message.php?msg=ERROR: phone number is not ten digits :/");
		exit();
	}
}


if (isset($_POST['bio']) && $_POST['bio'] != ''){

	$bio = $_POST['bio'];
	$bio = mysqli_real_escape_string($db_conx, $bio);
	$sql = "UPDATE users SET bio='$bio' WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);	

}

//change session and cookies!!!!!!!!!!!!!!!!!!!!!!!!!

if (isset($_POST['pword']) && $_POST['pword'] != ''){
	if(isset($_POST['cpword']) && $_POST['cpword'] == $_POST['pword']){
		$p_hash = md5($_POST['pword']);
	}else{
		//passwords do not match
		header("location: ../message.php?msg=ERROR: Passwords dont match homie");
		exit();
	}
	$sql = "UPDATE users SET password='$p_hash' WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$_SESSION['password'] = $p_hash;
	setcookie("pass", $p_hash, strtotime( '+30 days' ), "/", "", "", TRUE);
}
mysqli_close($db_conx);	

header("location: ../user.php?u=$log_username");
exit();
?>