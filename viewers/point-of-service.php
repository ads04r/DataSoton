<?php

function weekday_sort($a, $b)
{
	$wd = array("mon"=>1, "tue"=>2, "wed"=>3, "thu"=>4, "fri"=>5, "sat"=>6, "sun"=>7);
	$aa = $wd[strtolower(substr($a, 0, 3))];
	$bb = $wd[strtolower(substr($b, 0, 3))];
	if($aa == $bb)
	{
		return 0;
	}
	else
	{
		if($aa < $bb)
		{
			return -1;
		}
		else
		{
			return 1;
		}
	}
}

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "gr","http://purl.org/goodrelations/v1#" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "event","http://purl.org/NET/c4dm/event.owl#" );
$graph->ns( "tl","http://purl.org/NET/c4dm/timeline.owl#" );
$entity = $graph->resource( $uri );
$rdesc = $entity->prepareDescription();
$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );
$rdesc->addRoute( "rooms:occupant/foaf:mbox" );
$rdesc->addRoute( "-foaf:depicts/*" );
$rdesc->addRoute( "-foaf:depicts/*/rdf:type" );
$rdesc->addRoute( "-foaf:depicts/*/rdfs:label" );
$rdesc->addRoute( "-foaf:depicts/dct:creator/foaf:homepage" );
$rdesc->addRoute( "-foaf:depicts/dct:creator/foaf:name" );
$rdesc->addRoute( "-foaf:depicts/dct:license/rdfs:label" );
$rdesc->addRoute( "-sr:within/rdf:type" );
$rdesc->addRoute( "-sr:within/rdfs:label" );
$rdesc->addRoute( "-sr:within/geo:lat" );
$rdesc->addRoute( "-sr:within/geo:long" );
$rdesc->addRoute( "*/geo:lat" );
$rdesc->addRoute( "*/geo:long" );
$rdesc->addRoute( "event:time/*" );
$rdesc->addRoute( "-gr:hasPOS" );
$rdesc->addRoute( "-gr:hasPOS/*" );
$rdesc->addRoute( "gr:hasOpeningHoursSpecification/*" );
$rdesc->addRoute( "gr:hasOpeningHoursSpecification/*/rdfs:label" );
$rdesc->addRoute( "-gr:availableAtOrFrom/*" );
$rdesc->addRoute( "-gr:availableAtOrFrom/gr:includes/*" );
$n = $rdesc->loadSPARQL( $endpoint );

if( $format == "sparql" )
{
	header("Content-type: text/plain");
	exit();
}

if( $format != "html" )
{
        # Unless this is HTML just let Graphite serve the document.
        if( !$rdesc->handleFormat( $format ) ) { print "404!\n"; }
        exit();
}

print("<p>" . $uri . "</p>");

$rdf = $rdesc->toGraph();
$item = $rdf->resource($uri);

// ## SIDE BAR ##

print("<div class=\"datasotonacuk_rightsidebar\">");

foreach($item->all("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within") as $loc)
{
	if(($loc->has("http://www.w3.org/2003/01/geo/wgs84_pos#lat")) & ($loc->has("http://www.w3.org/2003/01/geo/wgs84_pos#long")))
	{
		$lat = $loc->get("http://www.w3.org/2003/01/geo/wgs84_pos#lat")->toString();
		$lon = $loc->get("http://www.w3.org/2003/01/geo/wgs84_pos#long")->toString();
		print("<img src=\"/graphics/staticmaplite/staticmap.php?center=" . $lat . "," . $lon . "&zoom=17&size=300x300&markers=" . $lat . "," . $lon . ",ol-marker-green\" width=\"300\" height=\"300\"><br>");
	}
}

if($item->has("http://purl.org/goodrelations/v1#hasOpeningHoursSpecification"))
{
	$opening_hours = array();
	$dt = time();
	foreach($item->all("http://purl.org/goodrelations/v1#hasOpeningHoursSpecification") as $time)
	{
		$dts = strtotime("" . $time->get("http://purl.org/goodrelations/v1#validFrom"));
		$dte = strtotime("" . $time->get("http://purl.org/goodrelations/v1#validThrough"));
		if(($dt < $dts) | ($dt > $dte))
		{
			continue;
		}
		$day = preg_replace("/(.*)#([^#]+)/", "$2", $time->get("http://purl.org/goodrelations/v1#hasOpeningHoursDayOfWeek")->toString());
		$opening_hours[] = $day . "</td><td>" . preg_replace("/([0-9]+):([0-9]+)(.*)/", "$1:$2", $time->get("http://purl.org/goodrelations/v1#opens")) . "</td><td>-</td><td>" . preg_replace("/([0-9]+):([0-9]+)(.*)/", "$1:$2", $time->get("http://purl.org/goodrelations/v1#closes"));
	}
	if(count($opening_hours) > 0)
	{
		usort($opening_hours, "weekday_sort");
		print("<h3>Opening Hours</h3><table><tr><td>" . implode("</td></tr><tr><td>", $opening_hours) . "</td></tr></table>");
	}
}


if($item->has("-http://purl.org/goodrelations/v1#availableAtOrFrom"))
{
	$offerings = array();
	foreach($item->all("-http://purl.org/goodrelations/v1#availableAtOrFrom") as $offering)
	{
		if((!($offering->isType("http://purl.org/goodrelations/v1#Offering"))) | ($offering->has("http://purl.org/openorg/priceListSection")) | ($offering->has("http://purl.org/goodrelations/v1#hasPriceSpecification")))
		{
			continue;
		}
		$offerings[] = $offering->get("http://purl.org/goodrelations/v1#includes")->prettyLink();
	}

	if(count($offerings) > 0)
	{
		print("<h3>Available Here</h3>");
		print("<table><tr><td>");
		print(implode("</td></tr><tr><td>", $offerings));
		print("</td></tr></table>");
	}
}

print("</div>");

// ## MAIN CONTENT ##

if($item->has("http://purl.org/dc/terms/description"))
{
	print($item->get("http://purl.org/dc/terms/description"));
}

if($item->has("-http://purl.org/goodrelations/v1#hasPOS"))
{
	print("<p>Run by: " . $item->get("-http://purl.org/goodrelations/v1#hasPOS")->prettyLink() . "</p>");
}

if(($item->has("http://id.southampton.ac.uk/ns/workstationFreeSeats")) & ($item->has("http://id.southampton.ac.uk/ns/workstationSeats")))
{
	$free = (int) $item->get("http://id.southampton.ac.uk/ns/workstationFreeSeats")->toString();
	$total = (int) $item->get("http://id.southampton.ac.uk/ns/workstationSeats")->toString();
	print("<div>");
	print("<p>This cluster currently has " . $free . " of " . $total . " workstations available for use.</p>");
	print("</div>");
}

// ## ADDITIONAL CONTENT ##

if($item->has("http://purl.org/openorg/ukfhrsRatingValue"))
{
	print("<hr>");

	$rating = (int) $item->get("http://purl.org/openorg/ukfhrsRatingValue")->toString();
	$rating_date = 0;
	$rating_page = "";
	if($item->has("http://purl.org/openorg/ukfhrsRatingDate"))
	{
		$rating_date = strtotime($item->get("http://purl.org/openorg/ukfhrsRatingDate") . " 12:00:00 GMT");
	}
	if($item->has("http://purl.org/openorg/ukfhrsPage"))
	{
		$rating_page = $item->get("http://purl.org/openorg/ukfhrsPage");
	}
	print("<div>");
	if(strlen($rating_page) > 0)
	{
		print("<a href=\"" . $rating_page . "\">");
	}
	print("<img src=\"http://data.southampton.ac.uk/resources/images/fhrs/small/72ppi/fhrs_" . $rating . "_en-gb.jpg\" width=\"120\" height=\"61\" border=\"0\" alt=\"FHRS Rating " . $rating . "\">");
	if(strlen($rating_page) > 0)
	{
		print("</a>");
	}
	if($rating_date > 0)
	{
		print("<br>Inspected: " . date("jS M Y", $rating_date));
	}
	print("</div>");
}

$rdesc->handleFormat("rdf.html");
