<?php

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "event","http://purl.org/NET/c4dm/event.owl#" );
$graph->ns( "tl","http://purl.org/NET/c4dm/timeline.owl#" );
$building = $graph->resource( $uri );
$rdesc = $building->prepareDescription();
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
$rdesc->addRoute( "*/geo:lon" );
$rdesc->addRoute( "event:time/*" );
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

$rdesc->handleFormat("rdf.html");
