<?php

$graph = $rdesc->toGraph();

/*
foreach($graph->allOfType("http://purl.org/goodrelations/v1#LocationOfSalesOrServiceProvisioning")->sort("rdfs:label") as $pos)
{
	$offers = $pos->get("-http://purl.org/goodrelations/v1#availableAtOrFrom")->all("http://purl.org/goodrelations/v1#includes");
	if(count($offers) == 0)
	{
		continue;
	}

	print("<div class=\"littlebox\">");
	print("<h4>" . $pos->prettyLink() . "</h4>");
	print("<ul>");
	foreach($offers as $offer)
	{
		print("<li> " . $offer->prettyLink() . "</li>");
	}
	print("</ul></div>");
}
*/

foreach($graph->allOfType("http://purl.org/goodrelations/v1#LocationOfSalesOrServiceProvisioning")->sort("rdfs:label")->get("-http://purl.org/goodrelations/v1#availableAtOrFrom")->all("http://purl.org/goodrelations/v1#includes")->sort("rdfs:label") as $service)
{
	$typeuri = "" . $service;
	if( (strcmp($service, "http://id.southampton.ac.uk/generic-products-and-services/BicycleParking") == 0) |
	    (strcmp($service, "http://id.southampton.ac.uk/generic-products-and-services/CoachTravel") == 0) |
	    (strcmp($service, "http://id.southampton.ac.uk/generic-products-and-services/LongDistanceCoaches") == 0) |
	    (strcmp($service, "http://id.southampton.ac.uk/generic-products-and-services/PayAndDisplayCarParking") == 0) |
	    (strcmp($service, "http://id.southampton.ac.uk/generic-products-and-services/CarParking") == 0) )
	{
		continue;
	}
	$poss = $service->all("-http://purl.org/goodrelations/v1#includes")->all("http://purl.org/goodrelations/v1#availableAtOrFrom")->sort("rdfs:label");
	if(count($poss) == 0)
	{
//		continue;
	}
	print("<h3>" . $service->prettyLink() . "</h3>");
	print("<table class=\"datasotonacuk_searchresults\">");
	foreach($poss as $pos)
	{
		print("<tr><td><img src=\"" . $pos->get("http://purl.org/openorg/mapIcon")->toString() . "\"></td><td>" . $pos->prettyLink() . "</td></tr>");
	}
	print("</table>");
}
