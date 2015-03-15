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
	<title>Finna Reply?</title>
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
		function replyToStatus(sid,user,ta,btn){
			var data = _(ta).value;
			if(data == ""){
				return false;
			}
			_("replyBtn_"+sid).disabled = true;
			var ajax = ajaxObj("POST", "php_parsers/status_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					var datArray = ajax.responseText.trim().split("|");
					if(datArray[0] == "reply_ok"){
						var rid = datArray[1];
						data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
						window.location.assign("index.php");
					} else {
						alert(ajax.responseText);
					}
				}
			}
			ajax.send("action=status_reply&sid="+sid+"&user="+user+"&data="+data);
		}
		function runit(){
			document.getElementById("replytext").focus();
		}
		window.onload = runit;
	</script>
</head>
<body>
	<div id="pageMiddle">
		<?php
			if($user_ok == true){
				if (isset($_GET['sid'])){
					$statusid = $_GET['sid'];
					echo '<textarea id="replytext_'.$statusid.'" class="replytext" style="font-family: Helvetica, sans-serif;" onkeyup="statusMax(this,150)" placeholder="Write a comment here..."></textarea>
								<button class="statusbutts" id="replyBtn_'.$statusid.'" onclick="replyToStatus('.$statusid.',\''.$log_username.'\',\'replytext_'.$statusid.'\',\'this\')">
								Reply</button>';
				}
			}
		?>
	</div>
</body>
</html>