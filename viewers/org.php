<?php

$query = $_SERVER['QUERY_STRING'];
$tabs = array(
	array("id"=>"", "text"=>"Detail", "script"=>"org-detail.php"),
	array("id"=>"facilities", "text"=>"Facilities", "script"=>"org-facilities.php"),
	array("id"=>"people", "text"=>"People", "script"=>"org-people.php"),
	array("id"=>"teaching", "text"=>"Teaching", "script"=>"org-teaching.php"),
	array("id"=>"vacancies", "text"=>"Vacancies", "script"=>"org-vacancies.php"),
	array("id"=>"debug", "text"=>"?", "script"=>"org-debug.php")
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
$res = $graph->resource( $uri );
$rdesc = $res->prepareDescription();
$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );

// Page-specific data
if(strlen($query) == 0)
{
	$rdesc->addRoute( "-org:hasSubOrganization/*" );
	$rdesc->addRoute( "-rooms:occupant/*" );
}
if(strcmp($query, "facilities") == 0)
{
	$rdesc->addRoute( "-oo:organizationPart/*" );
	$rdesc->addRoute( "-oo:organizationPart/oo:contact/*" );
}
if(strcmp($query, "people") == 0)
{
	$rdesc->addRoute( "foaf:member/*" );
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
