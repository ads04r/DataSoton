<?php

include_once("src/opendata.php");
$type = "http://vocab.deri.ie/rooms#Building";
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

print("<h2>Places</h2>\n");

$reslist = (array) $apps->allOfType($type);
foreach($reslist as $res)
{
        print("<h3><a href=\"" . $res . "\">" . $res->label() . "</a></h3>");
        print("<p><a style=\"font-family: sans-serif; font-size: small;\" href=\"" . $res . "\">" . $res . "</a></p>");
        if($res->has("dct:description"))
        {
                print("<p>" . $res->get("dct:description") . "</p>");
        }
}
