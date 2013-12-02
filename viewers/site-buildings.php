<?php

function id_sort($a, $b)
{
	if($a['id'] < $b['id'])
	{
		return -1;
	}
	if($a['id'] > $b['id'])
	{
		return 1;
	}
	return strcmp($a['number'], $b['number']);
}

print("<table><tr>");

$buildings = array();

print("<td><p>Sorted by name</p><table>");
foreach($site->all("-sr:within")->sort("rdfs:label") as $building)
{
	if(!($building->isType("http://vocab.deri.ie/rooms#Building")))
	{
		continue;
	}
	$blgno = "" . $building->get("skos:notation");
	$blgid = (int) $blgno;
	if($blgid >= 1000)
	{
		continue;
	}
	$item = array();
	$item['number'] = $blgno;
	$item['id'] = (int) $item['number'];
	$item['name'] = $building->prettyLink() . "";
	if($item['id'] == 0)
	{
		continue;
	}
	print("<tr><td>" . $item['number'] . "</td><td>" . $item['name'] . "</td></tr>");
	$buildings[] = $item;
}
print("</table></td>");

usort($buildings, "id_sort");

print("<td><p>Sorted by number</p><table>");
foreach($buildings as $building)
{
	print("<tr><td>" . $building['number'] . "</td><td>" . $building['name'] . "</td></tr>");
}
print("</table></td>");

print("</tr></table>");
