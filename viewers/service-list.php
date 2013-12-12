<?php

function thingsort($a, $b)
{
	$buildinga = "";
	$buildingb = "";
	$rooma = "";
	$roomb = "";
	$loca = "";
	$locb = "";

	@$buildinga = $a['building']['title'];
	@$buildingb = $b['building']['title'];
	if(strlen($buildinga) == 0)
	{
		@$buildinga = $a['site']['title'];
	}
	if(strlen($buildingb) == 0)
	{
		@$buildingb = $b['site']['title'];
	}
	@$rooma = $a['room']['uri'];
	@$roomb = $b['room']['uri'];
	@$loca = $a['location'];
	@$locb = $b['location'];

	if((strlen($buildinga) == 0) & (strlen($buildingb) > 0))
	{
		return 1;
	}
	if((strlen($buildinga) > 0) & (strlen($buildingb) == 0))
	{
		return -1;
	}

	$building = strnatcmp($buildinga, $buildingb);
	if($building != 0)
	{
		return $building;
	}
	$room = strcmp($rooma, $roomb);
	if($room != 0)
	{
		return $room;
	}
	return(strcmp($loca, $locb));
}

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "event","http://purl.org/NET/c4dm/event.owl#" );
$graph->ns( "tl","http://purl.org/NET/c4dm/timeline.owl#" );
$graph->ns( "gr","http://purl.org/goodrelations/v1#" );
$building = $graph->resource( $uri );
$rdesc = $building->prepareDescription();
$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );
$rdesc->addRoute( "-gr:includes" );
$rdesc->addRoute( "-gr:includes/gr:availableAtOrFrom" );
$rdesc->addRoute( "-gr:includes/gr:availableAtOrFrom/*" );
$rdesc->addRoute( "-gr:includes/gr:availableAtOrFrom/sr:within/*" );
//$rdesc->addRoute( "-gr:includes/sr:within/*" );
//$rdesc->addRoute( "-gr:includes/gr:availableAtOrFrom/sr:within/sr:within/*" );

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

$res = array();
foreach($item->all("-http://purl.org/goodrelations/v1#includes") as $posp)
{
	if(!($posp->has("http://purl.org/goodrelations/v1#availableAtOrFrom")))
	{
		continue;
	}
	$pos = $posp->get("http://purl.org/goodrelations/v1#availableAtOrFrom");
	$item = array();
	$item['title'] = "" . $pos->label();
	$item['uri'] = "" . $pos;
	foreach($pos->all("http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within") as $loc)
	{
		if($loc->isType("http://vocab.deri.ie/rooms#Site"))
		{
			$site = array();
			$site['title'] = "" . $loc->label();
			$site['uri'] = "" . $loc;
			$item['site'] = $site;
		}
		if($loc->isType("http://vocab.deri.ie/rooms#Building"))
		{
			$building = array();
			$building['title'] = "" . $loc->label();
			$building['uri'] = "" . $loc;
			$item['building'] = $building;
		}
		elseif($loc->isType("http://vocab.deri.ie/rooms#Room"))
		{
			$room = array();
			$room['title'] = "" . $loc->label();
			$room['uri'] = "" . $loc;
			$item['room'] = $room;
		}
		else
		{
			$location = "" . $loc->label();
			if(strcmp($location, "[NULL]") == 0)
			{
				$item['location'] = "" . $loc;
			}
			else
			{
				$item['location'] = $location;
			}
		}
	}
	if($pos->has("oo:mapIcon"))
	{
		$item['icon'] = "" . $pos->get("oo:mapIcon");
	}
	if(!(array_key_exists("location", $item)))
	{
		if(array_key_exists("building", $item))
		{
			$item['location'] = $item['building']['uri'];
		}
		elseif(array_key_exists("site", $item))
		{
			$item['location'] = $item['site']['uri'];
		}
	}
	if(strcmp($item['title'], "[NULL]") != 0)
	{
		$res[] = $item;
	}
}

usort($res, "thingsort");

$lastbuilding = "";
print("<table class=\"datasotonacuk_searchresults\">");
foreach($res as $item)
{
	if((array_key_exists("site", $item)) & (!(array_key_exists("building", $item))))
	{
		$item['building'] = $item['site'];
	}

	$building = "" . @$item['building']['title'];
	if(strcmp($building, $lastbuilding) != 0)
	{
		print("</table>");
		if(strlen($building) == 0)
		{
			print("<h3>Other places</h3>");
		}
		else
		{
			print("<h3>" . $building . "</h3>");
		}
		print("<table class=\"datasotonacuk_searchresults\">");
	}
	$lastbuilding = $building;

	print("<tr>");
	print("<td>");
	if(array_key_exists("icon", $item))
	{
		print("<img src=\"" . $item['icon'] . "\">");
	}
	print("</td>");
	print("<td>");
	print("<h4><a href=\"" . $item['uri'] . "\">" . $item['title'] . "</a></h4>");
	if((array_key_exists("building", $item)) | (array_key_exists("building", $item)))
	{
		print("<p>");
		if(array_key_exists("room", $item))
		{
			if(array_key_exists("building", $item))
			{
				$roomnum = preg_replace("/^(.*)-([0-9A-Za-z]+)$/", "$2", $item['room']['uri']);
				print("<a href=\"" . $item['room']['uri'] . "\">Room " . $roomnum . "</a>, ");
			}
			else
			{
				print("<a href=\"" . $item['room']['uri'] . "\">" . $item['room']['title'] . "</a>");
			}
		}
		if(array_key_exists("building", $item))
		{
			if(!(strcmp($item['building']['title'], "[NULL]") == 0))
			{
				print("<a href=\"" . $item['building']['uri'] . "\">" . $item['building']['title'] . "</a>");
			}
		}
		print("</p>");
	}
	else
	{
		if(array_key_exists("location", $item))
		{
			print("<p>" . $item['location'] . "</p>");
		} else {
			print("<p></p>");
		}
	}
	print("</td>");
	print("</tr>");
}
print("</table>");

//$rdesc->handleFormat("rdf.html");
