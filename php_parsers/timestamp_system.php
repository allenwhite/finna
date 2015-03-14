<?php

function convert_datetime($str) {
	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);
	date_default_timezone_set('Asia/Bangkok');
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	return $timestamp;
}

function makeAgo($timestamp){
	$difference = (time() - (7*60*60)) - $timestamp;
	$periods = array("s", "m", "h", "d", "w", "month");
	$lengths = array("60","60","24","7","4.35");
	for($j = 0; ($difference >= $lengths[$j]) && ($j < 4); $j++){
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1){ $periods[$j].= "";}
	$text = "$difference$periods[$j]";
	return $text;
}

?>