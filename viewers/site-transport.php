<?php

$stops = array();
$bikes = array();
$cars = array();
$coaches = array();

foreach($site->all("http://xmlns.com/foaf/0.1/based_near") as $thing)
{
	if(!($thing->isType("http://vocab.org/transit/terms/Stop")))
	{
		continue;
	}
	$stops[] = $thing;
}
foreach($site->all("http://xmlns.com/foaf/0.1/based_near")->all("owl:sameAs") as $thing)
{
	if(!($thing->isType("http://vocab.org/transit/terms/Stop")))
	{
		continue;
	}
	$stops[] = $thing;
}
foreach($site->all("-http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within") as $thing)
{
	if(!($thing->has("-http://purl.org/goodrelations/v1#availableAtOrFrom")))
	{
		continue;
	}
	if(!($thing->get("-http://purl.org/goodrelations/v1#availableAtOrFrom")->has("http://purl.org/goodrelations/v1#includes")))
	{
		continue;
	}
	$includes = "" . $thing->get("-http://purl.org/goodrelations/v1#availableAtOrFrom")->get("http://purl.org/goodrelations/v1#includes");
	if(strcmp($includes, "http://id.southampton.ac.uk/generic-products-and-services/BicycleParking") == 0)
	{
		$bikes[] = $thing;
	}
	if((strcmp($includes, "http://id.southampton.ac.uk/generic-products-and-services/PayAndDisplayCarParking") == 0) | (strcmp($includes, "http://id.southampton.ac.uk/generic-products-and-services/CarParking") == 0))
	{
		$cars[] = $thing;
	}
	if((strcmp($includes, "http://id.southampton.ac.uk/generic-products-and-services/CoachTravel") == 0) | (strcmp($includes, "http://id.southampton.ac.uk/generic-products-and-services/LongDistanceCoaches") == 0))
	{
		$coaches[] = $thing;
	}
}

if(count($stops) > 0)
{
	print("<h3>Bus Stops</h3><table>");
	foreach($stops as $stop)
	{
		print("<tr>");
		print("<td><img src=\"" . $stop->get("oo:mapIcon") . "\"><td>");
		print("<td>" . $stop->prettyLink() . "<td>");
		print("</tr>");
	}
	print("</table>");
}
if(count($coaches) > 0)
{
	print("<h3>Coach Stops</h3><table>");
	foreach($coaches as $stop)
	{
		print("<tr>");
		print("<td><img src=\"" . $stop->get("oo:mapIcon") . "\"><td>");
		print("<td>" . $stop->prettyLink() . "<td>");
		print("</tr>");
	}
	print("</table>");
}
if(count($bikes) > 0)
{
	print("<h3>Bicycle Parking</h3><table>");
	foreach($bikes as $bike)
	{
		print("<tr>");
		print("<td><img src=\"" . $bike->get("oo:mapIcon") . "\"><td>");
		print("<td>" . $bike->prettyLink() . "<td>");
		print("</tr>");
	}
	print("</table>");
}
if(count($cars) > 0)
{
	print("<h3>Car Parking</h3><table>");
	foreach($cars as $car)
	{
		print("<tr>");
		print("<td><img src=\"" . $bike->get("oo:mapIcon") . "\"><td>");
		print("<td>" . $car->prettyLink() . "<td>");
		print("</tr>");
	}
	print("</table>");
}
