<?php

add_filter('the_content', 'media_page_handler');

function media_page_handler($text)
{
	global $wpdb;
	$this_month = date("F Y");
	$day = strtotime("$this_month thursday");
	if ($day == false) {
		$next_meeting = "the first Thursday of the month"; }
	else if ($day < time()-86400) {
		$this_month = date("F Y", strtotime("next month first day"));
		$day = strtotime("$this_month thursday");
		$next_meeting = date("l, F jS", $day); 
	}
	else {
		$next_meeting = date("l, F jS", $day); }
	return str_replace("%next_meeting%", $next_meeting, $text);
}
?>
