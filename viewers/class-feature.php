<?php

function site_room_sort($a, $b)
{
	$la = @$a['building']['label'];
	$lb = @$b['building']['label'];
	$r = strcmp($la, $lb);
	if($r == 0)
	{
		$la = @$a['label'];
		$lb = @$b['label'];
		return(strnatcmp($la, $lb));
	}
	return($r);
}

print("<p>This is a feature associated with the following rooms.</p>");
$rooms = array();
foreach($graph->allOfType("http://id.southampton.ac.uk/ns/SyllabusLocation") as $room)
{
	$item = array();
	$item['uri'] = "" . $room;
	$item['label'] = "" . $room->label();
	if($room->has("oo:capacity"))
	{
		$item['capacity'] = (int) ("" . $room->get("oo:capacity"));
	}
	foreach($room->all("sr:within") as $area)
	{
		if(!($area->isType("http://vocab.deri.ie/rooms#Building")))
		{
			continue;
		}
		$building = array();
		$building['uri'] = "" . $area;
		$building['label'] = "" . $area->label();
		$item['building'] = $building;
	}
	$rooms[] = $item;
}

usort($rooms, "site_room_sort");

$lasttitle = "";
foreach($rooms as $room)
{
	if(!(array_key_exists("building", $room)))
	{
		continue;
	}
	$title = $room['building']['label'];
	if(strcmp($title, $lasttitle) != 0)
	{
		print("<h3>" . $room['building']['label'] . "</h3>");
	}
	print("<p><a href=\"" . $room['uri'] . "\">" . $room['label'] . "</a></p>");
	$lasttitle = $title;
}
