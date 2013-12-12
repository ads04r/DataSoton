<?php

function building_sort_bynumber($a, $b)
{
	return(strnatcmp($a['id'], $b['id']));
}

function building_sort_byname($a, $b)
{
	if((preg_match("/^[0-9]/", $a['name']) > 0) & (preg_match("/^[^0-9]/", $b['name']) > 0))
	{
		return 1;
	}
	if((preg_match("/^[^0-9]/", $a['name']) > 0) & (preg_match("/^[0-9]/", $b['name']) > 0))
	{
		return -1;
	}
	return(strnatcmp($a['name'], $b['name']));
}

include_once("src/opendata.php");
$type_site = "http://vocab.deri.ie/rooms#Site";
$type_blg = "http://vocab.deri.ie/rooms#Building";
$main_uri = "http://id.southampton.ac.uk/site/1";
$endpoint = $f3->get('sparql_endpoint');

$sites = getEntities($type_site, $endpoint);

$query = "SELECT DISTINCT * WHERE {
    ?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://vocab.deri.ie/rooms#Building> .
    ?uri <http://www.w3.org/2000/01/rdf-schema#label> ?label .
    ?uri <http://www.w3.org/2004/02/skos/core#notation> ?number .
    ?uri <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within> ?site .
}";
$buildings = array();
$result = sparql_get($endpoint, $query);
foreach($result as $row)
{
	$item = array();
	$item['id'] = "" . $row['number'];
	$item['name'] = "" . $row['label'];
	$item['uri'] = "" . $row['uri'];
	$item['site'] = "" . $row['site'];
	if(strlen($item['id']) > 4)
	{
		continue;
	}
	$buildings[] = $item;
	$sites->addCompressedTriple($item['uri'], "http://data.ordnancesurvey.co.uk/ontology/spatialrelations/within", $item['site']);
	$sites->addCompressedTriple($item['uri'], "rdf:type", $type_blg);
	$sites->addCompressedTriple($item['uri'], "rdfs:label", $item['name'], "literal");
	$sites->addCompressedTriple($item['uri'], "http://www.w3.org/2004/02/skos/core#notation", $item['id'], "literal");
}

if(strcmp($params['format'], "rdf") == 0)
{
	header("Content-type: application/rdf+xml");
	print($sites->serialize("RDFXML"));
	exit();
}
if(strcmp($params['format'], "ttl") == 0)
{
	header("Content-type: text/turtle");
	print($sites->serialize("Turtle"));
	exit();
}
if(strcmp($params['format'], "nt") == 0)
{
	header("Content-type: text/plain");
	print($sites->serialize("NTriples"));
	exit();
}

print("<h2>University of Southampton Places</h2>\n");
print("<h3>Sites</h3>");

$sitelinks = $sites->allOfType($type_site)->sort("rdfs:label");
$c = count($sitelinks);
$i = 0;
$cols = 2;
print("<table style=\"width: 100%; border-collapse: collapse;\"><tr>");
$maincampus = $sites->resource($main_uri);
print("<td><strong>" . $maincampus->prettyLink() . "</strong></td>");
$i++;
foreach($sites->allOfType($type_site)->sort("rdfs:label") as $site)
{
	$uri = "" . $site;
	if(strcmp($uri, $main_uri) == 0)
	{
		continue;
	}
	print("<td style=\"width: " . (100 / $cols) . "%;\">" . $site->prettyLink() . "</td>");
	$i++;
	if($i >= $cols)
	{
		print("</tr><tr>");
		$i = 0;
	}
}
print("<table><tr>");

print("<h3>Buildings</h3>");

print("<table style=\"width: 100%; border-collapse: collapse;\"><tr>");
print("<td style=\"width: 50%;\"><strong>By Name</strong></td>");
print("<td style=\"width: 50%;\"><strong>By Number</strong></td>");
print("</tr><tr>");

usort($buildings, "building_sort_byname");
print("<td><table style=\"border-collapse: collapse; font-size: 0.9em;\">");
foreach($buildings as $building)
{
	print("<tr><td style=\"max-width: 4em;\">" . $building['id'] . "</td><td><a href=\"" . $building['uri'] . "\">" . $building['name'] . "</a></td></tr>");
}
print("</table></td>");

usort($buildings, "building_sort_bynumber");
print("<td><table style=\"border-collapse: collapse; font-size: 0.9em;\">");
foreach($buildings as $building)
{
	print("<tr><td style=\"max-width: 4em;\">" . $building['id'] . "</td><td><a href=\"" . $building['uri'] . "\">" . $building['name'] . "</a></td></tr>");
}
print("</table></td>");

print("</tr></table>");
