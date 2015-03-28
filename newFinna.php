<?php
error_reporting(E_ALL);
	ini_set('display_errors', '1');

include_once("php_includes/check_login_status.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>Finna</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style/style.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script type="text/javascript">
		function statusMax(field, maxlimit) {//max length of a status
			if (field.value.length > maxlimit){
				alert(maxlimit+" maximum character limit reached");
				field.value = field.value.substring(0, maxlimit);
			}
		}
		function postToStatus(action,type,user,ta){
			var data = _(ta).value;
			if(data == ""){
				return false;
			}
			_("statusBtn").disabled = true;
			var url = "seeusers.php?u=" + user + "&st=" + data;
			window.location.assign(url);
			// var ajax = ajaxObj("POST", "php_parsers/status_system.php");
			// ajax.onreadystatechange = function() {
			// 	if(ajaxReturn(ajax) == true) {
			// 		var datArray = ajax.responseText.trim().split("|");
			// 		if(datArray[0] == "post_ok"){
			// 			var sid = datArray[1];
			// 			data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
			// 			window.location.assign("index.php");

			// 		} else {
			// 			alert(ajax.responseText);
			// 		}
			// 	}
			// }
			// ajax.send("action="+action+"&type="+type+"&user="+user+"&data="+data);
		}
		function runit(){
			var statusbox =document.getElementById("statustext");
			if ((typeof(statusbox) !== 'undefined') && (statusbox  !== null)) {
				statusbox.addEventListener('focus',function(){
					if(statusbox.value == ''){
						statusbox.value="I'm finna ";
					}
				}, false);
				statusbox.addEventListener('blur',function(){
					if(statusbox.value == ''){
						statusbox.value="I'm finna ";
					}
				}, false);
			}
		}
		window.onload = runit;
	</script>
</head>
<body>
	
	<div id="pageMiddle">
		<?php
			if($user_ok == true){
				echo '<textarea id="statustext" onkeyup="statusMax(this,150)" style="font-family: Helvetica, sans-serif;" placeholder="I\'m finna..." ></textarea>';
				echo ' <img src="time_icon.png" class="editicons"> <textarea id="statustime" onkeyup="statusMax(this,150)" style="font-family: Helvetica, sans-serif;" placeholder="Time" ></textarea>';
				echo ' <img src="location_icon.png" class="editicons"><textarea id="statuslocation" onkeyup="statusMax(this,150)" style="font-family: Helvetica, sans-serif;" placeholder="Location" ></textarea>';
				echo '<button class="statusbutts" id="statusBtn" onclick="postToStatus(\'status_post\',\'a\',\''.$log_username.'\',\'statustext\')">Post</button>';
///////////////////////////////////////////////////////////////////////////////////////////urlencode this status shit
			}
		?>
	</div>
</body>
</html>