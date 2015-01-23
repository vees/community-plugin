<?php

if (php_sapi_name() != 'cli') {
	add_filter('the_content', 'opendays_page_handler');
}

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

function get_google_calendar_feed()
{
	$filename = 'https://www.google.com/calendar/feeds/' .
                'dgif6f88nm2oas7mp4s6mugvlc%40group.calendar.google.com/public/full?' .
                'alt=jsonc&start-min=2014-01-01T00:00:00&start-max=2015-12-31T00:00:00&max-results=1000';
	$filename="/home/hpca/cache/46c68ed4180acf6a33ecbb829bd7cddd.html";
	$string = file_get_contents($filename);

	$var = json_decode($string);
	return $var;
}

function showing_day_list($google_feed)
{
	$showing_days = array();
	$var = $google_feed;
	if (count($var->items) == 0)
	{
		return "Could not access calendar.";
	}
	foreach ($var->items as $item)
	{
		if ($item->summary == "Hall Showing" || $item->summary == "Hall Viewing")
		{
			if (!empty($item->start->dateTime)) $showing_days[] = $item->start->dateTime;
			else if (!empty($item->start->date)) $showing_days[] = $item->start->date;
		}
	}

	sort($showing_days);

	$showing_days = array_slice($showing_days, 0, 10);
	$showdays_output.="<ul>\n";
	
	foreach($showing_days as $showday)
	{
		$showdays_output .= "<li>" . date('l F j', strtotime($showday)). "\n";
	}
	if (count($showing_days) == 0)
	{
		$showdays_output .= "<li>No showings are currently scheduled.</li>\n";
	}
	$showdays_output .= "</ul>\n";

	return $showdays_output;
}

function strikeout_taken_days($saturdays, $google_feed)
{
	$var = $google_feed;

	foreach ($var->items as $item)
	{
		$saturdayKey = get_saturday_key($item->start->date);
		if ($item->title == "Residents Only")
		{
			$saturdays[$saturdayKey] .= " <strong>(Residents Only)</strong>";
		}
		else if (isset($saturdays[$saturdayKey]) && ($item->visibility == "private" || $item->title == "Do Not Rent"))
		{
			$saturdays[$saturdayKey] = "<strike>".$saturdays[$saturdayKey]."</strike>";
		}
	}
	return $saturdays;
}

function get_saturday_key($gce1)
{
	#print $gce1;
	$gce1 = substr($gce1, 0, 10);
	$dayOfWeek = date("w", strtotime($gce1));
	#print $dayOfWeek;
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

	if (strpos($text, '%opendays%') === FALSE && strpos($text, '%showings%') === FALSE) 
		return $text;

	$google_feed = get_google_calendar_feed();
	$saturdays = list_of_saturdays();
	$saturdays = strikeout_taken_days($saturdays, $google_feed);

	$output_list = "<ul>\n";
	foreach ($saturdays as $weekend)
	{
		$output_list .= "<li>$weekend</li>\n";
	}
	$output_list .= "</ul>\n";

	$showings = showing_day_list($google_feed);
	#print $showings;

	if (true)
	{
		$outtext = str_replace("%opendays%", $output_list, $text);
		$outtext = str_replace("%showings%", $showings, $outtext);
	}
	else{
		$nocalendar = "<p>Calendar information is not available.</p>";
		$outtext = str_replace("%opendays%", $nocalendar, $text);
		$outtext = str_replace("%showings%", $nocalendar, $outtext);
	}

	return $outtext;
}
if (php_sapi_name() == 'cli')
{
	print opendays_page_handler('%opendays%');
	print opendays_page_handler('%showings%');
}

?>
