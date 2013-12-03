<?php

$query = $_SERVER['QUERY_STRING'];
$tabs = array(
	array("id"=>"", "text"=>"Detail", "script"=>"building-detail.php"),
	array("id"=>"facilities", "text"=>"Facilities", "script"=>"building-facilities.php"),
	array("id"=>"services", "text"=>"Services", "script"=>"building-services.php"),
	array("id"=>"energy", "text"=>"Energy", "script"=>"building-energy.php")
);

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "org","http://www.w3.org/ns/org#" );
$graph->ns( "oo","http://purl.org/openorg/" );
$graph->ns( "gr","http://purl.org/goodrelations/v1#" );
$graph->ns( "sd","http://www.semanticdesktop.org/ontologies/2007/03/22/nfo#" );
$building = $graph->resource( $uri );
$rdesc = $building->prepareDescription();
$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );

if(strlen($query) == 0)
{
	$rdesc->addRoute( "-foaf:depicts/*" );
	$rdesc->addRoute( "-foaf:depiction/*" );
	$rdesc->addRoute( "-foaf:depicts/*/rdf:type" );
	$rdesc->addRoute( "-foaf:depicts/*/rdfs:label" );
	$rdesc->addRoute( "-foaf:depicts/dct:creator/foaf:homepage" );
	$rdesc->addRoute( "-foaf:depicts/dct:creator/foaf:name" );
	$rdesc->addRoute( "-foaf:depicts/dct:license/rdfs:label" );
	$rdesc->addRoute( "rooms:occupant/foaf:mbox" );
	$rdesc->addRoute( "rooms:occupant/org:hasSubOrganization" );
}

if(strcmp($query, "services") == 0)
{
	$rdesc->addRoute( "-sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/oo:mapIcon" );
	$rdesc->addRoute( "-sr:within/rdf:type/rdfs:label" );
	$rdesc->addRoute( "-sr:within/sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/sr:within/rdf:type/rdfs:label" );
	$rdesc->addRoute( "-sr:within/soton:workstationSeats" );
	$rdesc->addRoute( "-sr:within/soton:workstationFreeSeats" );
	//$rdesc->addRoute( "-sr:within/-gr:availableAtOrFrom/gr:includes" );
	//$rdesc->addRoute( "-sr:within/-gr:availableAtOrFrom/gr:includes/rdfs:label" );
}

if(strcmp($query, "facilities") == 0)
{
	$rdesc->addRoute( "-sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/rdf:type/rdfs:label" );
	$rdesc->addRoute( "-sr:within/sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/sr:within/rdf:type/rdfs:label" );

	//$rdesc->addRoute( "-sr:within/oo:primaryContact" );
	//$rdesc->addRoute( "-sr:within/oo:capacity" );
	//$rdesc->addRoute( "-sr:within/oo:hasFeature" );
	$rdesc->addRoute( "-sr:within/*" );
	$rdesc->addRoute( "-sr:within/oo:primaryContact/*" );
	$rdesc->addRoute( "-sr:within/oo:hasFeature/rdf:type/rdfs:label" );

	$rdesc->addRoute( "-sr:within/-foaf:depicts" );
	//$rdesc->addRoute( "-sr:within/-foaf:depicts/sd:height" );
	$rdesc->addRoute( "-sr:within/-foaf:depicts/sd:width" );
}
$n = $rdesc->loadSPARQL( $endpoint );

if( $format == "map" )
{
	print($graph->toOpenStreetMap());
}

if( $format != "html" )
{
        # Unless this is HTML just let Graphite serve the document.
        if( !$rdesc->handleFormat( $format ) ) { print "404!\n"; }
        exit;
}

//print("<p>" . $uri . "</p>");
print(renderUri($uri));

$script = "";
print("<div class=\"datasotonacuk_tabs\">");
print("<ul>");
foreach($tabs as $tab)
{
	if(strcmp($tab['id'], $query) == 0)
	{
		print("<li class=\"tabcurrent\"><a href=\"?" . $tab['id'] . "\">" . $tab['text'] . "</a></li>");
		$script = $tab['script'];
	}
	else
	{
		print("<li><a href=\"?" . $tab['id'] . "\">" . $tab['text'] . "</a></li>");
	}
}

print("</ul>");
print("</div>");

if(strlen($script) == 0)
{
	$f3->error(404);
} else {
	include("./viewers/" . $script);
}
