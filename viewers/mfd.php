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

print("</div>");

// ## MAIN CONTENT ##

if($item->has("http://purl.org/dc/terms/description"))
{
	print($item->get("http://purl.org/dc/terms/description"));
}
else
{
	print("<p>This is a multi-function device, provided by iSolutions, as part of the managed print service. The managed print service provides printing, copying and scanning services to members of the University.</p>");
}

if($item->has("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within"))
{
	print("<h3>Device Location</h3><table>");
	foreach($item->all("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within") as $loc)
	{
		print("<tr><td>" . $loc->prettyLink() . "</td></tr>");
	}
	print("</table>");
}

if($item->has("-http://purl.org/goodrelations/v1#availableAtOrFrom"))
{
	print("<h3>Functions Available</h3><table>");
	foreach($item->all("-http://purl.org/goodrelations/v1#availableAtOrFrom") as $feature)
	{
		print("<tr><td>" . $feature->get("http://purl.org/goodrelations/v1#includes")->prettyLink() . "</td></tr>");
	}
	print("</table>");
}

$rdesc->handleFormat("rdf.html");
