<?php

function sortDatasets($a, $b)
{
	$ta = (string) $a->label();
	$tb = (string) $b->label();
	return(strcmp($ta, $tb));
}

include_once("src/opendata.php");
$type = "http://www.w3.org/ns/dcat#Dataset";
$apps = getEntities($type, $f3->get('sparql_endpoint'));

if(strcmp($params['format'], "rdf") == 0)
{
        header("Content-type: application/rdf+xml");
        print($apps->serialize("RDFXML"));
        exit();
}
if(strcmp($params['format'], "ttl") == 0)
{
        header("Content-type: text/turtle");
        print($apps->serialize("Turtle"));
        exit();
}
if(strcmp($params['format'], "nt") == 0)
{
        header("Content-type: text/plain");
        print($apps->serialize("NTriples"));
        exit();
}

print("<h2>Data Catalogue</h2>\n");
print("<p>This page serves as a list of all our datasets. Each dataset is available in (at least) Turtle and RDF/XML formats, as well as an HTML description.");
print(" Depending on the data, other formats may be available (eg KML). ");
print("If you want to query this data using SPARQL, please use our <a href=\"http://sparql.data.southampton.ac.uk/\">SPARQL endpoint</a>.</p>");


$reslist = (array) $apps->allOfType($type);
usort($reslist, "sortDatasets");
foreach($reslist as $res)
{
        print("<h3><a href=\"" . $res . "\">" . $res->label() . "</a></h3>");
        print("<p><a style=\"font-family: sans-serif; font-size: small;\" href=\"" . $res . "\">" . $res . "</a></p>");
        if($res->has("dct:description"))
        {
                print("<p>" . $res->get("dct:description") . "</p>");
        }
}
