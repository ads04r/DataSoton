<?php

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "org","http://www.w3.org/ns/org#" );
$building = $graph->resource( $uri );
$rdesc = $building->prepareDescription();
$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );
$rdesc->addRoute( "rooms:occupant/foaf:mbox" );
$rdesc->addRoute( "rooms:occupant/org:hasSubOrganization" );
$rdesc->addRoute( "-foaf:depicts/*" );
$rdesc->addRoute( "-foaf:depicts/*/rdf:type" );
$rdesc->addRoute( "-foaf:depicts/*/rdfs:label" );
$rdesc->addRoute( "-foaf:depicts/dct:creator/foaf:homepage" );
$rdesc->addRoute( "-foaf:depicts/dct:creator/foaf:name" );
$rdesc->addRoute( "-foaf:depicts/dct:license/rdfs:label" );
$rdesc->addRoute( "-sr:within/rdf:type" );
$rdesc->addRoute( "-sr:within/rdfs:label" );
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

print("<p>" . $uri . "</p>");

$query = $_SERVER['QUERY_STRING'];
$tabs = array(
	array("id"=>"", "text"=>"Detail", "script"=>"building-detail.php"),
	array("id"=>"facilities", "text"=>"Facilities", "script"=>"building-facilities.php"),
	array("id"=>"energy", "text"=>"Energy", "script"=>"building-energy.php")
);

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

//print("<li class=\"tabcurrent\"><a href=\"?\">Details</a></li>");
//print("<li><a href=\"?facilities\">Facilities</a></li>");
//print("<li><a href=\"?energy\">Energy</a></li>");

print("</ul>");
print("</div>");

if(strlen($script) == 0)
{
	$f3->error(404);
} else {
	include("./viewers/" . $script);
}
