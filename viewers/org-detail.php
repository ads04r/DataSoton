<?php

print("<p>NOTE: This is an information page on data.southampton.ac.uk. It is not intended to replace the organisation's official home page.</p>");

$homepage = "";
$superparts = array();
$subparts = array();
$buildings = array();

if($res->has("foaf:homepage"))
{
	$homepage = $res->get("foaf:homepage");
}

foreach($res->all("org:hasSubOrganization")->sort("rdfs:label") as $suborg)
{
	$subparts[] = "" . $suborg->prettyLink();
}

foreach($res->all("-org:hasSubOrganization")->sort("rdfs:label") as $org)
{
	$superparts[] = "" . $org->prettyLink();
}
foreach($res->all("-rooms:occupant")->sort("rdfs:label") as $place)
{
	if(!($place->isType("rooms:Building")))
	{
		continue;
	}
	$buildings[] = $place->prettyLink();
}

if(strlen($homepage) > 0)
{
	print("<table>");
	print("<tr><td>Homepage:</td><td><a href=\"" . $homepage . "\">" . $homepage . "</a></td></tr>");
	print("</table>");
}
print("<h3>Organisation</h3>");

print("<table>");

$i = 0;
foreach($superparts as $org)
{
	print("<tr><td>");
	if($i == 0)
	{
		print("Part of:");
	}
	print("</td><td>" . $org . "</td></tr>");
	$i++;
}
$i = 0;
foreach($subparts as $subpart)
{
	print("<tr><td>");
	if($i == 0)
	{
		print("Sub-parts:");
	}
	print("</td><td>" . $subpart . "</td></tr>");
	$i++;
}

print("</table>");

if(count($buildings) > 0)
{
	print("<h3>Locations</h3>");
	print("<table>");
	foreach($buildings as $blg)
	{
		print("<tr><td>" . $blg . "</td></tr>");
	}
	print("</table>");
}
