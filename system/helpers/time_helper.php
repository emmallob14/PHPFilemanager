<?php
function time_diff($timestamp) {
	date_default_timezone_set("UTC");
	
	$strTime = array("second", "minute", "hour", "day", "week", "month", "year");
	$length = array("60","60","24","4","30","12","10");
		
	$currentTime = time();
	if($currentTime >= $timestamp) {
		$diff = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "s";
	} else {
		$diff = $timestamp - time();
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "s";
	}
	
}