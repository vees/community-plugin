<?php

add_filter('the_content', 'opendays_page_handler');

function list_of_saturdays()
{
	$daysToSat = 6 - date("w");

	$firstSat = date('Y-m-d', strtotime(date("Y-m-d") . " + $daysToSat days"));

	#print "$daysToSat Days to Saturday\n"; 
	#print "First Saturday is $firstSat\n";

	$saturdays = array();

	for ($weekCount = 0; $weekCount <= 52; $weekCount++)
	{
		$saturdayKey = date('Y-m-d', strtotime($firstSat . " + " . $weekCount*7 . " days"));
		$thisSaturday = date('l F j', strtotime($firstSat . " + " . $weekCount*7 . " days"));
		$thisSunday = date('l F j', strtotime($firstSat . " + " . ($weekCount*7+1) . " days"));
		$membersOnly = ($weekCount >= 26) ? " <strong>(Residents Only)</strong>" : "";
		$saturdays[$saturdayKey] = "$thisSaturday or $thisSunday$membersOnly";
	}
	return $saturdays;
}

function strikeout_taken_days($saturdays)
{
	$string = file_get_contents('https://www.google.com/calendar/feeds/' .
		'dgif6f88nm2oas7mp4s6mugvlc%40group.calendar.google.com/public/full?' . 
		'alt=jsonc&start-min=2014-01-01T00:00:00&start-max=2015-12-31T00:00:00&max-results=500');

	$var = json_decode($string);

	foreach ($var->data->items as $item)
	{
		$saturdayKey = get_saturday_key($item->when[0]->start);
		#print $saturdayKey . "\n";
		if (isset($saturdays[$saturdayKey]))
		{
			#unset($saturdays[$saturdayKey]);
			$saturdays[$saturdayKey] = "<strike>$saturdays[$saturdayKey]</strike>";
		}	
	}
	return $saturdays;
}

function get_saturday_key($gce1)
{
	$gce1 = substr($gce1, 0, 10);
	#echo "$gce1 to:\n";
	$dayOfWeek = date("w", strtotime($gce1));
	if ($dayOfWeek == 6)
	{
		return date('Y-m-d', strtotime($gce1));	
	}
	if ($dayOfWeek == 0)
	{
		return date('Y-m-d', strtotime($gce1 . " - 1 days"));
	}
	return "";	
}


function opendays_page_handler($text)
{
	global $wpdb;

	if (strpos($text, '%opendays%') === FALSE) 
		return $text;

	$saturdays = list_of_saturdays();
	$saturdays = strikeout_taken_days($saturdays);

	$output_list = "<ul>\n";
	foreach ($saturdays as $weekend)
	{
		$output_list .= "<li>$weekend</li>\n";
	}
	$output_list .= "</ul>\n";

	return str_replace("%opendays%", $output_list, $text);
}

#print opendays_page_handler('%opendays%');

?>
