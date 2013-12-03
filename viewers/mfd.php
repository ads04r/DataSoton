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
$rdesc->addRoute( "gr:hasMakeAndModel/*" );
$rdesc->addRoute( "gr:hasMakeAndModel/gr:hasManufacturer/*" );
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

print(renderUri($uri));

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

print("<table>");
if($item->has("http://purl.org/goodrelations/v1#hasMakeAndModel"))
{
	print("<tr><td>Model:</td><td>" . $item->get("http://purl.org/goodrelations/v1#hasMakeAndModel")->label() . "</td></tr>");
}
if($item->has("http://purl.org/goodrelations/v1#hasManufacturer"))
{
	print("<tr><td>Manufacturer:</td><td>" . $item->get("http://purl.org/goodrelations/v1#hasManufacturer")->label() . "</td></tr>");
}
print("</table>");

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

print("<h2>Managed Print Information</h2>");

print("<p>The University has dozens of printers all over its campuses, and all are linked to the same network. This means you can print a document from your desktop, an iSolutions workstation or your laptop or mobile device and collect it from any printer in the University.</p>");
print("<p>In order to use this system you need to make sure a compatible printer app or driver is installed on your devices, and (optionally) register your ID card on the printer system. Below are some links that will help you do this.</p>");

print("<p><a href=\"https://www.secureapps.soton.ac.uk/PublicProxy/files.aspx?f=https://intranet.soton.ac.uk/sites/iSolutions/user-services/How%20to/How%20to%20register%20your%20University%20card%20for%20printing.pdf\">Card Registration Guide</a> [PDF]</p>");
print("<p><a href=\"https://www.secureapps.soton.ac.uk/PublicProxy/files.aspx?f=https://intranet.soton.ac.uk/sites/iSolutions/user-services/How%20to/Printing%20guide.pdf\">Printing Guide</a> [PDF]</p>");
print("<p><a href=\"http://www.southampton.ac.uk/isolutions/services/printing_on_managed_mfd/how_do_i.php\">Managed Print \"How Do I..?\" page</a></p>");

if($item->has("http://purl.org/goodrelations/v1#hasMakeAndModel"))
{
	if($item->get("http://purl.org/goodrelations/v1#hasMakeAndModel")->has("foaf:homepage"))
	{
		print("<p><a href=\"" . $item->get("http://purl.org/goodrelations/v1#hasMakeAndModel")->get("foaf:homepage") . "\">" . $item->get("http://purl.org/goodrelations/v1#hasMakeAndModel")->label() . " product page</a> (external website)</p>");
	}
}

print("<h3>Printing FAQs</h3>");
print("<p><a href=\"http://www.southampton.ac.uk/isolutions/services/follow_me_print_for_students/faq.php\">For Students</a></p>");
print("<p><a href=\"http://www.southampton.ac.uk/isolutions/services/follow_me_print_for_staff/faq.php\">For Staff</a></p>");
