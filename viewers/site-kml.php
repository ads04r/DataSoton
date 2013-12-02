<?php

/*  This is one of the few files on this version of the site which actually
    makes SPARQL calls. The reason is that it's much faster, and the many
    processes that are involved in KML generation from RDF make this necessary.

    Basically, this script is only called if a KML version of a site is
    requested. It draws a custom map depending on which tab is visible.

    The obvious downside is that this script must be maintained along with the
    main 'site.php' script, although it's not likely to change unless we
    get hold of some ground-breaking dataset.
*/

$site_uri = $uri;

// ########### BUILDINGS ######################################################

if(strcmp($query, "") == 0)
{

	$endpoint = $f3->get("sparql_endpoint");
	$query = "SELECT DISTINCT ?blg ?label ?polygon WHERE {
		    ?blg <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://vocab.deri.ie/rooms#Building> .
		    ?blg <http://purl.org/dc/terms/spatial> ?polygon .
		    ?blg <http://www.w3.org/2000/01/rdf-schema#label> ?label .
		    ?blg <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within> <" . $site_uri . ">
		  }";

	$data = sparql_get($endpoint, $query);
	$g = new Graphite();
	foreach($data as $row)
	{
		$uri = "" . $row['blg'];
		$title = "" . $row['label'];
		$points = "" . $row['polygon'];
		if(preg_match("/^POLYGON/", $points) > 0)
		{
			$g->addTriple($uri, "http://www.w3.org/2000/01/rdf-schema#label", $title, "literal");
			$g->addTriple($uri, "http://purl.org/dc/terms/spatial", $points, "literal");
			$g->addTriple($uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", "http://vocab.deri.ie/rooms#Building");
		}
	}

	header("Content-type: application/vnd.google-earth.kml+xml");
	print($g->toKml());

	exit();

}

// ########### TRANSPORT ######################################################

if(strcmp($query, "transport") == 0)
{
	$g = new Graphite();

	$endpoint = $f3->get("sparql_endpoint");
	$query = "SELECT DISTINCT ?uri ?icon ?label ?lat ?lon WHERE {
	 	    ?offer <http://purl.org/goodrelations/v1#includes> <http://id.southampton.ac.uk/generic-products-and-services/BicycleParking> .
		    ?offer <http://purl.org/goodrelations/v1#availableAtOrFrom> ?uri .
		    ?uri <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within> <" . $site_uri . "> .
		    ?uri <http://purl.org/openorg/mapIcon> ?icon .
		    ?uri <http://www.w3.org/2000/01/rdf-schema#label> ?label .
		    ?uri <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat .
		    ?uri <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon .
		  }";

	$data = sparql_get($endpoint, $query);
	foreach($data as $row)
	{
		$uri = "" . $row['uri'];
		$g->addTriple($uri, "http://purl.org/openorg/mapIcon", $row['icon']);
		$g->addTriple($uri, "http://www.w3.org/2000/01/rdf-schema#label", $row['label'], "literal");
		$g->addTriple($uri, "http://www.w3.org/2003/01/geo/wgs84_pos#lat", $row['lat'], "http://www.w3.org/2001/XMLSchema#float");
		$g->addTriple($uri, "http://www.w3.org/2003/01/geo/wgs84_pos#long", $row['lon'], "http://www.w3.org/2001/XMLSchema#float");
		$g->addTriple($uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", "http://purl.org/goodrelations/v1#LocationOfSalesOrServiceProvisioning");
	}

	$query = "SELECT DISTINCT ?uri ?icon ?label ?lat ?lon WHERE {
		    ?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://transport.data.gov.uk/def/naptan/BusStop> .
		    ?sameas <http://www.w3.org/2002/07/owl#sameAs> ?uri .
		    <" . $site_uri . "> <http://xmlns.com/foaf/0.1/based_near> ?sameas .
		    ?uri <http://purl.org/openorg/mapIcon> ?icon .
		    ?uri <http://www.w3.org/2000/01/rdf-schema#label> ?label .
		    ?uri <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat .
		    ?uri <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon .
		  }";

	$data = sparql_get($endpoint, $query);
	foreach($data as $row)
	{
		$uri = "" . $row['uri'];
		$g->addTriple($uri, "http://purl.org/openorg/mapIcon", $row['icon']);
		$g->addTriple($uri, "http://www.w3.org/2000/01/rdf-schema#label", $row['label'], "literal");
		$g->addTriple($uri, "http://www.w3.org/2003/01/geo/wgs84_pos#lat", $row['lat'], "http://www.w3.org/2001/XMLSchema#float");
		$g->addTriple($uri, "http://www.w3.org/2003/01/geo/wgs84_pos#long", $row['lon'], "http://www.w3.org/2001/XMLSchema#float");
		$g->addTriple($uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", "http://transport.data.gov.uk/def/naptan/BusStop");
	}

	header("Content-type: application/vnd.google-earth.kml+xml");
	print($g->toKml());

	exit();

}
