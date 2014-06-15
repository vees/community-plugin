<?php

add_filter('the_content', 'codeenforcement_page_handler');

function codeenforcement_page_handler($text)
{
	global $wpdb;
	if (strpos($text, '%codeenforcement%') === FALSE)
                return $text;

	return str_replace("%codeenforcement%", file_get_contents("http://harfordpark.com/api/complaints/html/"), $text);
}
?>
