<?php
//very important that any file including this file to also include check_login_status.php as well
if($user_ok == true) {
    $rightloginLink = '<a href="newFinna.php"><img style="width:75px;height:75px;" src="images/compose.png" alt="newFinna" title="Im finna..."></a>&nbsp;&nbsp;&nbsp;';
    $leftloginLink = '&nbsp;&nbsp;&nbsp;<a href="logout.php">Log Out</a>';
}else{
	$rightloginLink = '';
	$leftloginLink = '';
}
?>

<div id="pageTop">
	<div id="pageTopWrap">
		<table class="pageHeadTable">
			<tr>
				<td id="leftLoginLogout">
					<?php echo $leftloginLink; ?>
				</td>
				<td id="pageTopLogo">
					<img src="images/logo.png" alt="Finna Logo" title="Whatchu Finna do?">
				</td>
				<td id="rightLoginLogout">
					<?php echo $rightloginLink; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
