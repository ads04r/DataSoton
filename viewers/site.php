<?php

$query = $_SERVER['QUERY_STRING'];
if(strlen($query) > 0)
{
	$query_array = explode("&", $query);
	$query = $query_array[0];
}

if($format == "kml")
{
	include("./viewers/site-kml.php");
}

$tabs = array(
        array("id"=>"", "text"=>"Buildings", "script"=>"site-buildings.php"),
        array("id"=>"services", "text"=>"Services", "script"=>"site-services.php"),
        array("id"=>"transport", "text"=>"Transport", "script"=>"site-transport.php")
);

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "gr", "http://purl.org/goodrelations/v1#" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "event","http://purl.org/NET/c4dm/event.owl#" );
$graph->ns( "tl","http://purl.org/NET/c4dm/timeline.owl#" );
$graph->ns( "skos","http://www.w3.org/2004/02/skos/core#" );
$graph->ns( "travel","http://vocab.org/transit/terms/" );
$graph->ns( "oo","http://purl.org/openorg/" );
$site = $graph->resource( $uri );
$rdesc = $site->prepareDescription();

if(strcmp($query, "") == 0)
{
	$rdesc->addRoute( "-sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/skos:notation" );
}

if(strcmp($query, "services") == 0)
{
	$rdesc->addRoute( "-sr:within/-gr:availableAtOrFrom/oo:mapIcon" );
	$rdesc->addRoute( "-sr:within/-gr:availableAtOrFrom/gr:includes/*" );
	$rdesc->addRoute( "-sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/oo:mapIcon" );
}

if(strcmp($query, "transport") == 0)
{
	$rdesc->addRoute( "-sr:within/-gr:availableAtOrFrom/oo:mapIcon" );
	$rdesc->addRoute( "-sr:within/-gr:availableAtOrFrom/gr:includes/*" );
	$rdesc->addRoute( "-sr:within/rdf:type" );
	$rdesc->addRoute( "-sr:within/rdfs:label" );
	$rdesc->addRoute( "-sr:within/oo:mapIcon" );
	$rdesc->addRoute( "foaf:based_near/*" );
	$rdesc->addRoute( "foaf:based_near/owl:sameAs/*" );
	$rdesc->addRoute( "foaf:based_near/owl:sameAs/-travel:stop/rdfs:label" );
	$rdesc->addRoute( "foaf:based_near/owl:sameAs/-travel:stop/rdf:type" );
}

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

$kml_url = "http://data.southampton.ac.uk/" . preg_replace("|^(.+)//([^/]+)/(.*)$|", "$3", $uri) . ".kml";
print("<iframe class=\"widemap\" border=\"none\" scrolling=\"none\" src=\"http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=" . urlencode($kml_url) . "&aq=&output=embed&t=k\"></iframe>");

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
